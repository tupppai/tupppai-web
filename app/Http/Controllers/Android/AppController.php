<?php namespace App\Http\Controllers\Android;

use App\Services\App as sApp;

class AppController extends ControllerBase{
    public function get_app_listAction(){
        $apps = sApp::getAppList();

        return $this->output( $apps );
    }

    public function shareAction() {
        // 类型: 普通分享，上传之后的分享
        $type       = $this->get('type', 'int');
        $target_id  = $this->get('target_id', 'int');
        $width      = $this->get('width', 'int', 320);

        if(!$target_id) {
            return error( 'WRONG_ARGUMENTS', '目标id不存在');
        }
        if(!$type) {
            return error( 'WRONG_ARGUMENTS', '请确定是要分享求助还是作品');
        }

        $data = sApp::shareApp( $type, $target_id, $width );

        return $this->output( $data );
    }
}
