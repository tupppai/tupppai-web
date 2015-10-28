<?php
namespace App\Http\Controllers\Main;

use App\Services\Reply As sReply;
use App\Services\Ask As sAsk;
use App\Services\User As sUser;

class ReplyController extends ControllerBase {

    public function index(){
        $ask_id = $this->post('ask_id', 'int');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 300);
        $uid  = $this->post('uid', 'int', $this->_uid);

        $cond = array(
            'replies.uid'=>$uid,
            'replies.ask_id'=>$ask_id
        );

        $reply_id = $this->get('reply_id', 'int');
        if($reply_id) {
            $reply    = sReply::getReplyById($reply_id);
            $replies = sReply::getAskRepliesWithOutReplyId( $reply->ask_id, $reply_id, $page, $size );

            if( $page == 1 ){
                $reply = sReply::detail($reply);
                array_unshift($replies, $reply);
            }
        }
        else {
            $replies = sReply::getReplies( $uid, $cond, $page, $size);
        }
        //$replies = sReply::getUserReplies($uid, $page, $size, time());

        return $this->output($replies);
    }

    public function ask($reply_id) {
        $reply  = sReply::getReplyById($reply_id);
        $ask    = sAsk::getAskById($reply->ask_id);

        $ask    = sAsk::detail($ask);

        return $this->output($ask);
    }

    public function view($reply_id) {
        $reply = sReply::getReplyById($reply_id);
        $reply = sReply::detail($reply);

        return $this->output( $reply );
    }

    public function save() {
        $ask_id    = $this->post('ask_id', 'int');
        $upload_id = $this->post('upload_id', 'int');
        $desc      = $this->post('desc', 'string', '');

        $uid = $this->_uid;

        $reply = sReply::addNewReply($uid, $ask_id, $upload_id, $desc);

        return $this->output($reply);
    }

    //点赞
    public function upAskAction() {
        $id     = $this->get('id', 'int');
        $status = $this->get('status', 'int', 1);

        $ret    = sAsk::updateAskCount($id, 'up', $status);
        return $this->output();
    }
}
?>
