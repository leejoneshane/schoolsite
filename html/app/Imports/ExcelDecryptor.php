<?php

namespace App\Imports;

use RuntimeException;

class ExcelDecryptor
{
    private const ENDOFCHAIN = -2;
    private const FREESECT = -1;

    /**
     * 若為受密碼保護的 .xlsx (OLE2 加密容器)，解密後寫入暫存 .xlsx 並回傳暫存檔路徑；
     * 若未加密或非 .xlsx 加密格式則回傳 null。
     */
    public static function decryptXlsxIfNeeded(string $filePath, ?string $password, string $extension = ''): ?string
    {
        if ($password === null || $password === '') {
            return null;
        }

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

        $streams = self::readOleStreams($filePath, ['EncryptionInfo', 'EncryptedPackage']);
        $encryptionInfo = $streams['EncryptionInfo'] ?? null;
        $encryptedPackage = $streams['EncryptedPackage'] ?? null;

        // 一般未加密 .xls 亦為 OLE2 容器，但不含 EncryptionInfo / EncryptedPackage
        if ($encryptionInfo === null || $encryptedPackage === null) {
            if (strtolower($extension) === 'xlsx') {
                throw new RuntimeException('此 .xlsx 檔案格式異常，無法識別為有效的 Excel 檔案。');
            }
            return null;
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
     * 直接依 MS-CFB (OLE2) 規格讀取指定串流（修正 PhpSpreadsheet OLE\ChainedBlockStream
     * 讀取小於 4096 bytes 之 Mini-Stream 時誤用 bigBlockSize 導致讀出空資料的問題）。
     *
     * @param string[] $targetNames
     * @return array<string, string>
     */
    public static function readOleStreams(string $filePath, array $targetNames): array
    {
        $data = file_get_contents($filePath);
        if ($data === false || strlen($data) < 512) {
            throw new RuntimeException('無法讀取 OLE2 檔案標頭。');
        }

        $sectorShift = unpack('v', substr($data, 0x1E, 2))[1];
        $miniSectorShift = unpack('v', substr($data, 0x20, 2))[1];
        $sectorSize = 1 << $sectorShift;
        $miniSectorSize = 1 << $miniSectorShift;

        $numFatSectors = unpack('V', substr($data, 0x2C, 4))[1];
        $firstDirSector = self::readInt32($data, 0x30);
        $miniStreamCutoff = unpack('V', substr($data, 0x38, 4))[1] ?: 4096;
        $firstMiniFatSector = self::readInt32($data, 0x3C);
        $firstDifatSector = self::readInt32($data, 0x44);
        $numDifatSectors = unpack('V', substr($data, 0x48, 4))[1];

        // 蒐集 FAT 磁區位置（前 109 個位於檔頭 0x4C，其餘於 DIFAT 鏈）
        $fatSectors = [];
        for ($i = 0; $i < min(109, $numFatSectors); $i++) {
            $secId = self::readInt32($data, 0x4C + $i * 4);
            if ($secId >= 0) {
                $fatSectors[] = $secId;
            }
        }

        $difatSec = $firstDifatSector;
        $entriesPerDifat = intdiv($sectorSize, 4) - 1;
        for ($d = 0; $d < $numDifatSectors && $difatSec >= 0; $d++) {
            $offset = ($difatSec + 1) * $sectorSize;
            for ($i = 0; $i < $entriesPerDifat && count($fatSectors) < $numFatSectors; $i++) {
                $secId = self::readInt32($data, $offset + $i * 4);
                if ($secId >= 0) {
                    $fatSectors[] = $secId;
                }
            }
            $difatSec = self::readInt32($data, $offset + $entriesPerDifat * 4);
        }

        // 建立 FAT（Big Block Chain）
        $fat = '';
        foreach ($fatSectors as $secId) {
            $fat .= substr($data, ($secId + 1) * $sectorSize, $sectorSize);
        }

        // 建立 MiniFAT（Small Block Chain）
        $miniFat = '';
        $miniSec = $firstMiniFatSector;
        $visited = [];
        while ($miniSec >= 0 && $miniSec !== self::ENDOFCHAIN && $miniSec !== self::FREESECT) {
            if (isset($visited[$miniSec])) {
                break;
            }
            $visited[$miniSec] = true;
            $miniFat .= substr($data, ($miniSec + 1) * $sectorSize, $sectorSize);
            $miniSec = self::readInt32($fat, $miniSec * 4);
        }

        // 讀取目錄串流（Directory Stream）
        $dirBytes = self::readFatStream($data, $fat, $firstDirSector, $sectorSize);

        $rootStartSector = self::ENDOFCHAIN;
        $entries = [];
        $dirLen = strlen($dirBytes);
        for ($off = 0; $off + 128 <= $dirLen; $off += 128) {
            $entry = substr($dirBytes, $off, 128);
            $type = ord($entry[0x42]);
            if ($type === 0) {
                continue;
            }
            $nameSize = unpack('v', substr($entry, 0x40, 2))[1];
            $rawName = substr($entry, 0, min(64, max(0, $nameSize - 2)));
            $name = mb_convert_encoding($rawName, 'UTF-8', 'UTF-16LE');
            $startSec = self::readInt32($entry, 0x74);
            $size = unpack('V', substr($entry, 0x78, 4))[1];

            if ($type === 5) {
                $rootStartSector = $startSec;
            } elseif ($type === 2 && in_array($name, $targetNames, true)) {
                $entries[$name] = ['start' => $startSec, 'size' => $size];
            }
        }

        if (empty($entries)) {
            return [];
        }

        // 僅在有串流小於 miniStreamCutoff 時讀取 Root MiniStream 容器
        $miniStream = null;
        foreach ($entries as $info) {
            if ($info['size'] > 0 && $info['size'] < $miniStreamCutoff) {
                $miniStream = self::readFatStream($data, $fat, $rootStartSector, $sectorSize);
                break;
            }
        }

        $result = [];
        foreach ($entries as $name => $info) {
            if ($info['size'] === 0) {
                $result[$name] = '';
                continue;
            }
            if ($info['size'] < $miniStreamCutoff) {
                $raw = self::readMiniStream($miniStream ?? '', $miniFat, $info['start'], $miniSectorSize);
            } else {
                $raw = self::readFatStream($data, $fat, $info['start'], $sectorSize);
            }
            $result[$name] = substr($raw, 0, $info['size']);
        }

        return $result;
    }

    private static function readFatStream(string $fileData, string $fat, int $startSector, int $sectorSize): string
    {
        $out = '';
        $sec = $startSector;
        $visited = [];
        while ($sec >= 0 && $sec !== self::ENDOFCHAIN && $sec !== self::FREESECT) {
            if (isset($visited[$sec])) {
                break;
            }
            $visited[$sec] = true;
            $out .= substr($fileData, ($sec + 1) * $sectorSize, $sectorSize);
            $sec = self::readInt32($fat, $sec * 4);
        }
        return $out;
    }

    private static function readMiniStream(string $miniStream, string $miniFat, int $startSector, int $miniSectorSize): string
    {
        $out = '';
        $sec = $startSector;
        $visited = [];
        while ($sec >= 0 && $sec !== self::ENDOFCHAIN && $sec !== self::FREESECT) {
            if (isset($visited[$sec])) {
                break;
            }
            $visited[$sec] = true;
            $out .= substr($miniStream, $sec * $miniSectorSize, $miniSectorSize);
            $sec = self::readInt32($miniFat, $sec * 4);
        }
        return $out;
    }

    private static function readInt32(string $bytes, int $offset): int
    {
        if ($offset < 0 || $offset + 4 > strlen($bytes)) {
            return self::ENDOFCHAIN;
        }
        $val = unpack('V', substr($bytes, $offset, 4))[1];
        if ($val >= 0x80000000) {
            $val -= 0x100000000;
        }
        return $val;
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
