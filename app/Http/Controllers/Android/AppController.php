<?php namespace App\Http\Controllers\Android;

use App\Services\App as sApp;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\IException as sIException;

use App\Models\Label as mLabel;

class AppController extends ControllerBase{

    public $_allow = array(
        'page',
    );

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

    public function pageAction() {
        $type = $this->get('type', 'int', mLabel::TYPE_ASK);
        $id   = $this->get('id', 'int', 1);

        $data = array();
        if($type == mLabel::TYPE_ASK) {
            $data['ask'] = sAsk::detail(sAsk::getAskById($id));
            $content = view("main.h5.ask", $data);
        }
        else if($type == mLabel::TYPE_REPLY) {
            $data['reply'] = sReply::getReplyById($id);
            $content = view("main.h5.reply", $data);
        }

        return $content;
    }

    public function exceptionsAction() {
        $message = $this->post('message', 'normal');

        sIException::addNewException($message);

        return $this->output();
    }
}
