<?php
namespace App\Http\Controllers\Main;

use App\Services\Reply As sReply;
use App\Services\Ask As sAsk;
use App\Services\Tag as sTag;
use App\Services\ThreadTag as sThreadTag;
use App\Services\User As sUser;
use App\Services\Upload As sUpload;
use App\Models\Reply as mReply;

class ReplyController extends ControllerBase {

    public function index(){

        $category_id = $this->post('category_id', 'int');
        $ask_id = $this->post('ask_id', 'int');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $uid  = $this->post('uid', 'int');

        $cond = array(
            'uid'=>$uid,
            'ask_id'=>$ask_id,
            'category_id' => $category_id
        );

        $replies = sReply::getReplies( $cond, $page, $size, $this->_uid );

        return $this->output($replies);
    }

    public function ask($ask_id) {
        //$reply  = sReply::getReplyById($reply_id);
        $ask    = sAsk::getAskById($ask_id);
        $page = $this->get('page', 'int');
        $size = $this->get('size', 'int');

        //whatif ask_id=0? activity
        $cond = array(
            'ask_id'=>$ask_id
        );

        $ask    = sAsk::detail($ask);
        $replies= sReply::getReplies( $cond, $page, $size );

        return $this->output(array(
            'ask'=>$ask,
            'replies'=>$replies
        ));
    }

    public function reply($reply_id) {
        $reply  = sReply::getReplyById($reply_id);
        $ask    = sAsk::getAskById($reply->ask_id);
        $page = $this->get('page', 'int');
        $size = $this->get('size', 'int');

        //whatif ask_id=0? activity
        $cond = array(
            'ask_id'=>$reply->ask_id
        );

        $ask    = sAsk::detail($ask);
        $replies= sReply::getReplies( $cond, $page, $size );

        return $this->output(array(
            'ask'=>$ask,
            'replies'=>$replies
        ));
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
        $tag_ids   = $this->post('tag_ids', 'string', '');
        if(empty($tag_id)){
            $tag_ids = explode(',',$tag_ids);
        }
        $category_id = $this->post('category_id', 'int');

        $uid = $this->_uid;

        //$reply = sReply::addNewReply($uid, $ask_id, $upload_id, $desc);
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_id, $desc, $category_id);
        //$upload = sUpload::updateImages( array($upload_id), $scales, $ratios );

        //写入reply标签
        foreach($tag_ids as $tag_id) {
                sThreadTag::addTagToThread( $this->_uid, mReply::TYPE_REPLY, $reply->id, $tag_id );
            }

        fire('TRADE_HANDLE_REPLY_SAVE',['reply'=>$reply]);
        return $this->output([
            'id' => $reply->id,
            'ask_id' => $ask_id,
            'category_id' => $category_id
        ]);
    }
}
?>
