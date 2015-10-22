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
        $uid  = $this->post('uid', 'int');

        $cond = array(
            'uid'=>$uid,
            'ask_id'=>$ask_id 
        );
        $replies = sReply::getRepliesByType($cond, 'hot', $page, $size);
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
        $page  = $this->get( 'page', 'int', 1 );
        $size  = $this->get( 'size', 'int', 10 );

        $reply    = sReply::getReplyById($reply_id);
        $replies = sReply::getAskRepliesWithOutReplyId( $reply->ask_id, $reply_id, $page, $size );

        if( $page == 1 ){
            $reply = sReply::detail($reply);
            array_unshift($replies, $reply);
        }

        return $this->output( $replies );
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
