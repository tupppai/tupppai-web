<?php
namespace App\Http\Controllers\Main2;

use App\Formats\Reply as fReply;
use App\Services\Reply As sReply;
use App\Services\Ask As sAsk;
use App\Services\ThreadTag As sThreadTag;

class ReplyController extends ControllerBase {

    public $_allow = '*';
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
        $data    = [];
        if(!empty($replies)){
            foreach($replies as $replie){
                $data[]    = fReply::index($replie);
            }
        }


        return $this->output($data);
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

        $ask    = sAsk::detailV2($ask);
        $replies= sReply::getRepliesV2( $cond, $page, $size );
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

    public function tag( $tag_id ){
        $page = $this->get('page', 'int', 1 );
        $size = $this->get('size', 'int', 15);

        $thread_tags = sThreadTag::getRepliesByTagId( $tag_id, $page, $size );

        $replies = [];
        foreach( $thread_tags as $thread_tag ){
            $replies[] = sReply::detail( $thread_tag->reply );
        }

        return $this->output($replies);
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

        $category_id = $this->post('category_id', 'int');
        $tags = $this->post('tags', 'string');

        $uid = $this->_uid;

        //$reply = sReply::addNewReply($uid, $ask_id, $upload_id, $desc);
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_id, $desc, $category_id, $tags);
        //$upload = sUpload::updateImages( array($upload_id), $scales, $ratios );

        fire('TRADE_HANDLE_REPLY_SAVE',['reply'=>$reply]);
        return $this->output([
            'id' => $reply->id,
            'ask_id' => $ask_id,
            'category_id' => $category_id
        ]);
    }
}
?>
