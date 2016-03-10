<?php namespace App\Http\Controllers\Api;

use App\Services\App as sApp;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\IException as sIException;

use App\Models\Label as mLabel;

class AppController extends ControllerBase{

    public $_allow = '*';

    public function get_app_listAction(){
        $apps = sApp::getAppList();

        return $this->output( $apps );
    }

    public function shareAction() {
        $type       = $this->get( 'type', 'int' );
        $target_id  = $this->get( 'target_id', 'int' );
        $share_type = $this->get( 'share_type', 'string');

        $width = $this->get( 'width', 'int', config('global.app.DEFAULT_SCREEN_WIDTH'));

        if(!$target_id) {
            return error( 'EMPTY_TARGET', '目标id不存在' );
        }
        if(!$type) {
            return error( 'EMPTY_TYPE', '请确定是要分享求助还是作品' );
        }

        $data = sApp::shareApp( $share_type, $type, $target_id, $width );

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
            $data['reply'] = sReply::detail(sReply::getReplyById($id));
            $content = view("main.h5.reply", $data);
        }
        return $content;
    }

    public function qrcodeAction() {
        $url = 'http://'.env('MAIN_HOST').'/main/img/WachatQrcode.png';

        return $this->output(array(
            'url'=>$url
        ));
    }

    public function exceptionsAction() {
        $message = $this->post('message', 'normal', '*Client didn\'t send message*');

        sIException::addNewException($message);

        return $this->output();
    }
}
