<?php namespace App\Http\Controllers\Api;
use App\Services\ActionLog as sActionLog;
use App\Services\Inform    as sInform;

class InformController extends ControllerBase{

    public function report_abuseAction(){
        $uid = $this->_uid;

        $target_type = $this->post('target_type','int');
        $target_id   = $this->post('target_id','int');
        $content     = $this->post('content', 'string');

        if( !$uid ){
            return error('EMPTY_UID', '请先登录');
        }

        if( !$target_id ){
            return error('EMPTY_ID');
        }

        $inform = sInform::report( $uid, $target_type, $target_id, $content );

        return $this->output( true  );
    }
}

