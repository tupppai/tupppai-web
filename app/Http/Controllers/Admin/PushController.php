<?php namespace App\Http\Controllers\Admin;

use Event, Log, Mail;

use App\Services\ActionLog as sActionLog;
use App\Models\ActionLog as mActionLog;

class PushController extends ControllerBase{

    public $_allow = array(
        'tower',
        'github',
        'fetchApk',
        'mailApk'
    );

    public function indexAction() {
        return $this->output();
    }

    public function updateApkAction() {
        $gitpushes  = sActionLog::fetchGithubPush();
        $towerpushes= sActionLog::fetchTowerTasks();

        return $this->output(array(
            'git'=>$gitpushes,
            'tasks'=>$towerpushes
        ));
    }

    public function fetchApkAction() {
        $gitpushes = sActionLog::fetchGithubPush(array(
            'project'=>'tupppai-android' 
        ), false);

        if($gitpushes->toArray()) 
            echo 1;
        else 
            echo 0;
        echo "\n";
        exit();
    }

    public function mailApkAction() {
        $this->layout = '';

        $email = 'billqiang@qq.com';
        $name  = 'junqiang';
        $cc    = array(
            '308598041@qq.com', 
            'iwyvern@foxmail.com', 
            '402377128@qq.com', 
            '1340949685@qq.com', 
            '353467140@qq.com'
        );

        $data = ['email'=>$email, 'name'=>$name, 'cc'=>$cc];
        $data['gitpushes']  = sActionLog::fetchGithubPush(array(
            'project'=>'tupppai-android' 
        ));
        $data['towerpushes']= sActionLog::fetchTowerTasks(array(
            'project'=>'安卓' 
        ));

        //return $this->output($data);

        Mail::send('admin/push/mailApk', $data, function($message) use($data) {
            $message->to($data['email'], $data['name'])
                ->cc($data['cc'])
                ->subject('图派版本体验');
        });
        echo ('success');
        exit();
    }

    public function list_pushesAction(){
        $model = new mActionLog('action_task');

        $cond = array();
        $cond['uid'] = array(
            $this->post('uid'),
            'LIKE',
            'AND'
        );
        $cond['status'] = mActionLog::STATUS_NORMAL;
        $join = array();
        $order = array();

        $data = $this->page($model, $cond, $join, $order );
        foreach ($data['data'] as $app) {
            $app->update_time = date('Y-m-d H:i:s', $app->update_time);
            $app->create_time = date('Y-m-d H:i:s', $app->create_time);
        }

        return $this->output_table($data);
    }

    public function towerAction() {
        $request_body = file_get_contents('php://input');

        sActionLog::addTowerTaskAction($request_body);
        Log::info('tower', array($request_body));
    }

    public function githubAction() {
        $request_body = file_get_contents('php://input');

        sActionLog::addGithubPushAction($request_body);
        Log::info('github', array($request_body));
    }

}
