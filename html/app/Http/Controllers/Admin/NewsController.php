<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Subscriber;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::all();
        return view('admin.news', ['news' => $news]);
    }

    public function add()
    {
        $models = $this->getAllModels();
        return view('admin.newsadd', ['models' => $models]);
    }

    public function insert(Request $request)
    {
        $caption = $request->input('caption');
        $model = $request->input('model');
        $loop = $request->input('loop');
        $day = $request->input('day');
        $weekday = $request->input('weekday');
        switch ($loop) {
            case 'auto':
                $cronjob = 'auto';
                break;
            case 'monthly':
                $cronjob = 'monthly.'.$day;
                break;
            case 'weekly':
                $cronjob = 'weekly.'.$weekday;
                break;
        }
        News::create([
            'name' => $caption,
            'model' => $model,
            'cron' => $cronjob,
        ]);
        return $this->index()->with('success', '電子報新增完成！');
    }

    public function edit($news)
    {
        $instance = News::find($news);
        $models = $this->getAllModels();
        return view('admin.newsedit', ['news' => $instance, 'models' => $models]);
    }

    public function update(Request $request, $news)
    {
        $caption = $request->input('caption');
        $model = $request->input('model');
        $loop = $request->input('loop');
        $day = $request->input('day');
        $weekday = $request->input('weekday');
        switch ($loop) {
            case 'auto':
                $cronjob = 'auto';
                break;
            case 'monthly':
                $cronjob = 'monthly.'.$day;
                break;
            case 'weekly':
                $cronjob = 'weekly.'.$weekday;
                break;
        }
        News::find($news)->update([
            'name' => $caption,
            'model' => $model,
            'cron' => $cronjob,
        ]);
        return $this->index()->with('success', '電子報更新完成！');
    }

    public function remove($news)
    {
        News::destroy($news);
        return $this->index()->with('success', '電子報已經刪除！');
    }

    public function subscribers($news)
    {
        $news = News::find($news);
        $subscribers = $news->subscribers;
        return view('admin.subscribers', ['news' => $news, 'subscribers' => $subscribers]);
    }

    public function insertSub(Request $request, $news)
    {
        $email = $request->input('email');
        $sub = Subscriber::findByEmail($email);
        if (!$sub) {
            $sub = Subscriber::create([
                'email' => $email,
            ]);
        }
        if (!($sub->verified)) {
            $sub->sendEmailVerificationNotification();
        }
        $sub->subscription($news);
        return $this->subscribers($news)->with('success', '訂閱戶新增完成！');
    }

    public function updateSub(Request $request, $news, $id)
    {
        $sub = Subscriber::find($id);
        $email = $request->input('email');
        $old = $sub->email;
        if ($email != $old) {
            $sub->update([ 'email' => $email]);
            $sub->sendEmailVerificationNotification();
            return $this->subscribers($news)->with('success', '訂閱戶電子郵件更新完成！');
        }
        return $this->subscribers($news)->with('error', '訂閱戶電子郵件與原有郵件地址相同！');
    }

    public function removeSub($news, $id)
    {
        $sub = Subscriber::find($id);
        $sub->cancel($news);
        return $this->subscribers($news)->with('success', '訂閱戶：'.$sub->email.' 已取消訂閱！');
    }

    public function getAllModels($base_folder = '', $sub_folder = "")
    {
        $modelList = [];
        if (empty($base_folder)) $base_folder = app_path() . "/Models";
        if (!empty($sub_folder)) {
            $path = $base_folder . '/' . $sub_folder;
        } else {
            $path = $base_folder;
        }
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' || $result === '..') continue;
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                $modelList = array_merge($modelList, $this->getAllModels($path, $result));
            } else {
                $content = file_get_contents($filename);
                if (preg_match('/implements Subscribeable/', $content)) {
                    $modelList[] = $this->convert_path($filename);
                }
            }
        }

        return $modelList;
    }

    private function convert_path($path)
    {
        preg_match('/\/var\/www\/html\/(.+)\.php$/', $path, $matches);
        return ucfirst(str_replace('/', '\\', $matches[1]));
    }

}
