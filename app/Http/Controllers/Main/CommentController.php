<?php  namespace App\Http\Controllers\Main;

use App\Services\Ask as sAsk,
    App\Services\Comment as sComment,
    App\Services\Reply as sReply;

use App\Models\Comment as mComment;

class CommentController extends ControllerBase {
    
    public $_allow = array('*');    

    public function index(){
        $type = $this->post('type', 'int', mComment::TYPE_ASK);
        $target_id = $this->post('target_id', 'int');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $uid  = $this->post('uid', 'int', $this->_uid);

        $comments = sComment::getComments($type, $target_id, $page, $size);
        
        return $this->output($comments);
    }


    public function view($id) {
        $comment = sComment::getCommentById($id);
        $comment = sComment::detail($comment);

        return $this->output($ask);
    }

    //点赞
    public function upAskAction() {
        $this->isLogin();

        $id     = $this->get('id', 'int');
        $status = $this->get('status', 'int', 1);

        $ret    = sAsk::updateAskCount($id, 'up', $status);
        return $this->output();
    }
}
?>
