<?php namespace App\Http\Controllers\Admin;

use Event, Log, Mail;

use App\Services\ActionLog as sActionLog;
use App\Models\ActionLog as mActionLog;

class PushController extends ControllerBase{

    public $_allow = array(
        'tower',
    );

    public function indexAction() {
        return $this->output();
    }

    public function mailAction() {
        $this->layout = '';

        $email = 'billqiang@qq.com';
        $name  = 'junqiang';

        $data = ['email'=>$email, 'name'=>$name];

        return $this->output($data);
        Mail::send('admin/push/mail', $data, function($message) use($data) {
            $message->to($data['email'], $data['name'])->subject('欢迎注册我们的网站，请激活您的账号！');
        });
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

    public function updateApkAction() {
        $gitpushes  = sActionLog::fetchGithubPush();
        $towerpushes= sActionLog::fetchTowerTasks();

        return $this->output(array(
            'git'=>$gitpushes,
            'tasks'=>$towerpushes
        ));
    }
}
