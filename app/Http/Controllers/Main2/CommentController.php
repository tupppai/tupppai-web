<?php  namespace App\Http\Controllers\Main2;

use App\Services\Ask as sAsk,
    App\Services\Comment as sComment,
    App\Services\Reply as sReply;

use App\Models\Comment as mComment;

class CommentController extends ControllerBase {
    
    public $_allow = '*';

    public function index(){
        $type = $this->post('type', 'int', mComment::TYPE_ASK);
        $target_id = $this->post('target_id', 'int');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $uid  = $this->post('uid', 'int', $this->_uid);

        $comment_type = $this->post('comment_type', 'string', 'new');

        $comments = sComment::getComments($type, $target_id, $page, $size);

        if($comment_type == 'hot'){
            $comments = $comments['hot_comments'];
        }
        else if($comment_type == 'new'){
            $comments = $comments['new_comments'];
        }
        
        return $this->output($comments);
    }


    public function view($id) {
        $comment = sComment::getCommentById($id);
        $comment = sComment::detail($comment);

        return $this->output($comment);
    }
    
    /**
     * 添加评论
     * $return integer  新增评论
     */
    public function save() {
        $this->isLogin(); 

        $uid        = $this->_uid;
        $content    = $this->post('content', 'string');
        $type       = $this->post('type', 'int');
        $target_id  = $this->post('id', 'int');
        $reply_to   = $this->post('reply_to', 'string', '0');
        $for_comment= $this->post('for_comment', 'int', '0');

        if ( empty($content) || empty($type) || empty($target_id) ) {
            return error('WRONG_ARGUMENTS');
        }

        $ret = sComment::addNewComment($uid, $content, $type, $target_id, $reply_to, $for_comment);

        return $this->output(['id'=>$ret->id]);
    }
}
?>
