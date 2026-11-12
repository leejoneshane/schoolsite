<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\Shared\OLE;
use RuntimeException;

class ExcelDecryptor
{
    /**
     * 若為受密碼保護的 .xlsx (OLE2 加密容器)，解密後寫入暫存 .xlsx 並回傳暫存檔路徑；
     * 若未加密或非 .xlsx 加密格式則回傳 null。
     */
    public static function decryptXlsxIfNeeded(string $filePath, ?string $password, string $extension = ''): ?string
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new RuntimeException('無法讀取上傳的檔案。');
        }

        $fh = fopen($filePath, 'rb');
        $magic = $fh ? fread($fh, 8) : '';
        if ($fh) {
            fclose($fh);
        }

        // 標準 ZIP (.xlsx) 或純文字 (.csv) 不需解密
        if (str_starts_with($magic, "PK\x03\x04") || $magic !== "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
            return null;
        }

        $ole = new OLE();
        $encryptionInfo = null;
        $encryptedPackage = null;
        try {
            $ole->read($filePath);

            foreach ($ole->_list as $index => $pps) {
                if ($pps->Type !== OLE::OLE_PPS_TYPE_FILE) {
                    continue;
                }
                if ($pps->Name === 'EncryptionInfo') {
                    $encryptionInfo = $ole->getData($index, 0, $pps->Size);
                } elseif ($pps->Name === 'EncryptedPackage') {
                    $encryptedPackage = $ole->getData($index, 0, $pps->Size);
                }
            }
        } finally {
            if (is_resource($ole->_file_handle)) {
                fclose($ole->_file_handle);
            }
        }

        // 一般未加密 .xls 亦為 OLE2 容器，但不含 EncryptionInfo / EncryptedPackage
        if ($encryptionInfo === null || $encryptedPackage === null) {
            if (strtolower($extension) === 'xlsx') {
                throw new RuntimeException('此 .xlsx 檔案格式異常，無法識別為有效的 Excel 檔案。');
            }
            return null;
        }

        if ($password === null || $password === '') {
            throw new RuntimeException('此 Excel 檔案已設定開啟密碼，請填寫「Excel 檔案密碼」後再試一次。');
        }

        $decryptedBytes = self::decryptPackage($encryptionInfo, $encryptedPackage, $password);
        if (!str_starts_with($decryptedBytes, "PK\x03\x04")) {
            throw new RuntimeException('Excel 檔案密碼錯誤或加密格式不受支援，無法解鎖檔案。');
        }

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'unlocked_' . bin2hex(random_bytes(8)) . '.xlsx';
        if (file_put_contents($tempPath, $decryptedBytes) === false) {
            throw new RuntimeException('無法建立暫存解密檔案。');
        }

        return $tempPath;
    }

    /**
     * 依 MS-OFFCRYPTO 規格解析 EncryptionInfo 並解密 EncryptedPackage。
     */
    public static function decryptPackage(string $encryptionInfo, string $encryptedPackage, string $password): string
    {
        if (strlen($encryptionInfo) < 8 || strlen($encryptedPackage) < 8) {
            throw new RuntimeException('加密檔案結構不完整。');
        }

        $header = unpack('vmajor/vminor/Vflags', substr($encryptionInfo, 0, 8));
        $major = $header['major'];
        $minor = $header['minor'];

        // Agile Encryption (Office 2010+): major=4, minor=4
        if ($major === 4 && $minor === 4) {
            return self::decryptAgile(substr($encryptionInfo, 8), $encryptedPackage, $password);
        }

        // Standard Encryption (Office 2007): major=3 or 4, minor=2
        if (($major === 3 || $major === 4) && $minor === 2) {
            return self::decryptStandard(substr($encryptionInfo, 8), $encryptedPackage, $password);
        }

        throw new RuntimeException("不支援的 Excel 加密版本（{$major}.{$minor}）。");
    }

    protected static function decryptAgile(string $xmlBytes, string $encryptedPackage, string $password): string
    {
        $xmlBytes = trim($xmlBytes, "\x00 \r\n\t");
        $prevUseErrors = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlBytes);
        libxml_clear_errors();
        libxml_use_internal_errors($prevUseErrors);

        if ($xml === false) {
            throw new RuntimeException('無法解析 Excel Agile 加密描述 XML。');
        }

        $ns = $xml->getNamespaces(true);
        $defaultNs = $ns[''] ?? 'http://schemas.microsoft.com/office/2006/encryption';
        $pNs = $ns['p'] ?? 'http://schemas.microsoft.com/office/2006/keyEncryptor/password';

        $root = $xml->children($defaultNs);
        $keyData = $root->keyData;
        $keyDataAttr = $keyData->attributes();

        $keyDataSalt = base64_decode((string) $keyDataAttr['saltValue'], true);
        $keyDataHashAlg = self::mapHashAlgorithm((string) $keyDataAttr['hashAlgorithm']);
        $keyDataCipherAlg = (string) $keyDataAttr['cipherAlgorithm'];
        $keyDataChaining = (string) $keyDataAttr['cipherChaining'];
        $keyDataKeyBits = (int) $keyDataAttr['keyBits'];
        $keyDataBlockSize = (int) $keyDataAttr['blockSize'];

        $encKeyNode = null;
        if (isset($root->keyEncryptors->keyEncryptor)) {
            foreach ($root->keyEncryptors->keyEncryptor as $encryptor) {
                $pChildren = $encryptor->children($pNs);
                if (isset($pChildren->encryptedKey)) {
                    $encKeyNode = $pChildren->encryptedKey;
                    break;
                }
            }
        }
        if ($encKeyNode === null) {
            throw new RuntimeException('找不到密碼加密金鑰節點，可能為憑證加密檔案。');
        }

        $ekAttr = $encKeyNode->attributes();
        $spinCount = (int) $ekAttr['spinCount'];
        $ekSalt = base64_decode((string) $ekAttr['saltValue'], true);
        $ekHashAlg = self::mapHashAlgorithm((string) $ekAttr['hashAlgorithm']);
        $ekCipherAlg = (string) $ekAttr['cipherAlgorithm'];
        $ekChaining = (string) $ekAttr['cipherChaining'];
        $ekKeyBits = (int) $ekAttr['keyBits'];
        $ekBlockSize = (int) $ekAttr['blockSize'];
        $encVerifierInput = base64_decode((string) $ekAttr['encryptedVerifierHashInput'], true);
        $encVerifierHash = base64_decode((string) $ekAttr['encryptedVerifierHashValue'], true);
        $encKeyValue = base64_decode((string) $ekAttr['encryptedKeyValue'], true);

        $ekCipher = self::mapOpenSslCipher($ekCipherAlg, $ekKeyBits, $ekChaining);
        $kdCipher = self::mapOpenSslCipher($keyDataCipherAlg, $keyDataKeyBits, $keyDataChaining);

        $pwUtf16 = mb_convert_encoding($password, 'UTF-16LE', 'UTF-8');
        $h = hash($ekHashAlg, $ekSalt . $pwUtf16, true);
        for ($i = 0; $i < $spinCount; $i++) {
            $h = hash($ekHashAlg, pack('V', $i) . $h, true);
        }

        $ekIv = self::agileIv($ekHashAlg, $ekSalt, null, $ekBlockSize);

        // 驗證密碼是否正確
        $verifierInputKey = self::agileKey($h, "\xfe\xa7\xd2\x76\x3b\x4b\x9e\x79", $ekHashAlg, $ekKeyBits);
        $verifierInput = openssl_decrypt($encVerifierInput, $ekCipher, $verifierInputKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $ekIv);

        $verifierHashKey = self::agileKey($h, "\xd7\xaa\x0f\x6d\x30\x61\x34\x4e", $ekHashAlg, $ekKeyBits);
        $verifierHash = openssl_decrypt($encVerifierHash, $ekCipher, $verifierHashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $ekIv);

        if ($verifierInput === false || $verifierHash === false) {
            throw new RuntimeException('Excel 解密驗證失敗。');
        }

        $computedHash = hash($ekHashAlg, $verifierInput, true);
        if (!hash_equals($computedHash, substr($verifierHash, 0, strlen($computedHash)))) {
            throw new RuntimeException('Excel 檔案密碼錯誤！');
        }

        // 解密主文件金鑰
        $secretKeyEncKey = self::agileKey($h, "\x14\x6e\x0b\xe7\xab\xac\xd0\xd6", $ekHashAlg, $ekKeyBits);
        $secretKey = openssl_decrypt($encKeyValue, $ekCipher, $secretKeyEncKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $ekIv);
        if ($secretKey === false) {
            throw new RuntimeException('無法解出 Excel 文件密鑰。');
        }
        $secretKey = substr($secretKey, 0, intdiv($keyDataKeyBits, 8));

        // 依 4096 bytes 區段逐段解密 EncryptedPackage
        $packageSize = unpack('P', substr($encryptedPackage, 0, 8))[1];
        $ciphertext = substr($encryptedPackage, 8);
        $segmentSize = 4096;
        $totalSegments = (int) ceil(strlen($ciphertext) / $segmentSize);

        $decrypted = '';
        for ($seg = 0; $seg < $totalSegments; $seg++) {
            $chunk = substr($ciphertext, $seg * $segmentSize, $segmentSize);
            $segIv = self::agileIv($keyDataHashAlg, $keyDataSalt, pack('V', $seg), $keyDataBlockSize);
            $plain = openssl_decrypt($chunk, $kdCipher, $secretKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $segIv);
            if ($plain === false) {
                throw new RuntimeException('Excel 檔案資料區段解密失敗。');
            }
            $decrypted .= $plain;
        }

        return substr($decrypted, 0, $packageSize);
    }

    protected static function decryptStandard(string $data, string $encryptedPackage, string $password): string
    {
        if (strlen($data) < 36) {
            throw new RuntimeException('Standard Encryption 標頭資料不足。');
        }

        $headerSize = unpack('V', substr($data, 0, 4))[1];
        $encHeader = substr($data, 4, $headerSize);
        $verifierOffset = 4 + $headerSize;

        $hdr = unpack('Vflags/VsizeExtra/ValgID/ValgIDHash/VkeySize/VproviderType', substr($encHeader, 0, 24));
        $keyBits = $hdr['keySize'] ?: 128;
        $algId = $hdr['algID'];

        $cipher = match ($algId) {
            0x660E, 0 => 'aes-128-ecb',
            0x660F => 'aes-192-ecb',
            0x6610 => 'aes-256-ecb',
            default => throw new RuntimeException(sprintf('不支援的 Standard Encryption 演算法 (0x%X)。', $algId)),
        };

        $verifierData = substr($data, $verifierOffset);
        $saltSize = unpack('V', substr($verifierData, 0, 4))[1];
        $salt = substr($verifierData, 4, $saltSize);
        $encVerifier = substr($verifierData, 4 + $saltSize, 16);
        $verifierHashSize = unpack('V', substr($verifierData, 4 + $saltSize + 16, 4))[1];
        $encVerifierHash = substr($verifierData, 4 + $saltSize + 20, 32);

        $pwUtf16 = mb_convert_encoding($password, 'UTF-16LE', 'UTF-8');
        $h = sha1($salt . $pwUtf16, true);
        for ($i = 0; $i < 50000; $i++) {
            $h = sha1(pack('V', $i) . $h, true);
        }
        $hFinal = sha1($h . pack('V', 0), true);

        $buf1 = str_repeat("\x36", 64);
        $buf2 = str_repeat("\x5c", 64);
        for ($i = 0; $i < 20; $i++) {
            $buf1[$i] = chr(ord($buf1[$i]) ^ ord($hFinal[$i]));
            $buf2[$i] = chr(ord($buf2[$i]) ^ ord($hFinal[$i]));
        }
        $x1 = sha1($buf1, true);
        $x2 = sha1($buf2, true);
        $secretKey = substr($x1 . $x2, 0, intdiv($keyBits, 8));

        $verifier = openssl_decrypt($encVerifier, $cipher, $secretKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
        $verifierHash = openssl_decrypt($encVerifierHash, $cipher, $secretKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
        if ($verifier === false || $verifierHash === false) {
            throw new RuntimeException('Excel 解密驗證失敗。');
        }

        $computedHash = sha1($verifier, true);
        if (!hash_equals($computedHash, substr($verifierHash, 0, $verifierHashSize))) {
            throw new RuntimeException('Excel 檔案密碼錯誤！');
        }

        $packageSize = unpack('P', substr($encryptedPackage, 0, 8))[1];
        $ciphertext = substr($encryptedPackage, 8);
        $decrypted = openssl_decrypt($ciphertext, $cipher, $secretKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
        if ($decrypted === false) {
            throw new RuntimeException('Excel 檔案解密失敗。');
        }

        return substr($decrypted, 0, $packageSize);
    }

    protected static function agileKey(string $h, string $blockKey, string $hashAlg, int $keyBits): string
    {
        $cbKey = intdiv($keyBits, 8);
        $derived = hash($hashAlg, $h . $blockKey, true);
        if (strlen($derived) < $cbKey) {
            $derived .= str_repeat("\x36", $cbKey - strlen($derived));
        }
        return substr($derived, 0, $cbKey);
    }

    protected static function agileIv(string $hashAlg, string $salt, ?string $blockKey, int $blockSize): string
    {
        $iv = $blockKey !== null ? hash($hashAlg, $salt . $blockKey, true) : $salt;
        if (strlen($iv) < $blockSize) {
            $iv .= str_repeat("\x36", $blockSize - strlen($iv));
        }
        return substr($iv, 0, $blockSize);
    }

    protected static function mapHashAlgorithm(string $name): string
    {
        return match (strtoupper($name)) {
            'SHA1', 'SHA-1' => 'sha1',
            'SHA256', 'SHA-256' => 'sha256',
            'SHA384', 'SHA-384' => 'sha384',
            'SHA512', 'SHA-512' => 'sha512',
            'MD5' => 'md5',
            default => throw new RuntimeException("不支援的雜湊演算法：{$name}"),
        };
    }

    protected static function mapOpenSslCipher(string $cipherAlg, int $keyBits, string $chaining): string
    {
        $alg = strtoupper($cipherAlg);
        $mode = match (strtoupper($chaining)) {
            'CHAININGMODECBC', 'CBC' => 'cbc',
            'CHAININGMODECFB', 'CFB' => 'cfb',
            default => throw new RuntimeException("不支援的加密鏈結模式：{$chaining}"),
        };

        if ($alg === 'AES') {
            return "aes-{$keyBits}-{$mode}";
        }

        throw new RuntimeException("不支援的對稱加密演算法：{$cipherAlg}");
    }
}
