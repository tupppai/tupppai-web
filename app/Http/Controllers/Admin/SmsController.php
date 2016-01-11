<?php namespace App\Http\Controllers\Admin;

use App\Models\Sms as mSms;

use App\Services\User as sUser;

class SmsController extends ControllerBase {

    public function indexAction(){

        return $this->output();
    }
        
    public function list_smsesAction(){
        $model = new mSms;

        $cond = array();
        $cond['to'] = array(
            $this->post('phone'),
            'LIKE',
            'AND'
        );
        $join = array();
        $order = array(
            'id desc',
        );

        $data = $this->page($model, $cond, $join, $order );
        foreach ($data['data'] as $sms) {
            $sms->sent_time = date('Y-m-d H:i:s', $sms->sent_time);

            $sms->is_reg   = '未注册';
            $sms->reg_time = '未注册';
            $user = sUser::getUserByPhone($sms->to);
            if($user) {
                $sms->is_reg  = 'ok';
                $sms->reg_time= date('Y-m-d H:i:s', $user->create_time);
            }
            $sms->is_used  = '未使用';
            if($sms->created_at == $sms->updated_at) {
                $sms->is_used  = 'ok';
            }
        }

        return $this->output_table($data);
    }

}
