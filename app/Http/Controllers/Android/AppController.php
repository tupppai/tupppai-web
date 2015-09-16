<?php namespace App\Http\Controllers\Android;

use App\Services\App as sApp;

class AppController extends ControllerBase{
    public function get_app_listAction(){
        $apps = sApp::getAppList();

        return $this->output( $apps );
    }

    public function shareAction() {
        // 类型: 普通分享，上传之后的分享
        $type       = $this->get( 'type', 'int' );
        #todo 接口调用不对，兼容模式
        $target_id  = $this->get( 'target_id', 'int' );
        $width = $this->get(
            'width',
            'int',
            config('global.app.DEFAULT_SCREEN_WIDTH')
        );

        if(!$target_id) {
            return error( 'EMPTY_TARGET', '目标id不存在' );
        }
        if(!$type) {
            return error( 'EMPTY_TYPE', '请确定是要分享求助还是作品' );
        }

        $data = sApp::shareApp( $type, $target_id, $width );

        return $this->output( $data );
    }
}
