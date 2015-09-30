<?php 
namespace App\Http\Controllers\Main;

use App\Services\Reply As sReply;
use App\Services\Ask As sAsk;

class ReplyController extends ControllerBase {

    public function index(){
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 300);
        $uid  = $this->post('uid', 'int', $this->_uid);

        $cond = array();
        $replies = sReply::getUserReplies($uid, $page, $size, time());
        
        return $this->output($replies);
    }

    public function view($id) {
        $reply = sReply::getReplyById($id);
        $ask   = sAsk::getAskById($reply->ask_id);
        $reply = sReply::detail($reply);
        $reply['ask'] = sAsk::detail($ask);

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
