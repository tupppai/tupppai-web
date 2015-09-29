<?php namespace App\Http\Controllers\Admin;

use App\Services\Comment as sComment;
use App\Services\CommentStock as sCommentStock;

use App\Jobs\PuppetComment;
use Queue, Carbon\Carbon;
class CommentController extends ControllerBase
{

    public function indexAction()
    {

        return $this->output();
    }

    public function list_commentsAction()
    {
        // 检索条件
        $cond = array();
        // 获取model
        $comment = new Comment;
        $cond[get_class($comment).'.uid'] = $this->post('uid');
        $cond[get_class(new User).'.username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );
        $cond['content']   = array(
            $this->post("content", "string"),
            "LIKE",
            "AND"
        );
        $join = array();
        $join['User'] = 'uid';

        $data  = $this->page($comment, $cond, $join);
        foreach($data['data'] as $row){
            $row->id =  $row->id;
            $row->uid = "评论用户ID:" . $row->uid;
            $row->oper = "<a class='edit'>编辑</a> <a class='delete'>删除</a>";
        }
        // 输出json
        return $this->output_table($data);
    }


    public function delete_commentAction()
    {
        // 获取model
        $comment = new sComment;
        // 检索条件
        $cond = array();
        $cond['cid']        = $this->post("cid", "int");
        $cond['comment_id']   = array(
            $cond['cid']
        );
        $data  = $this->page($user, $cond);
    }

    public function send_commentAction(){
        $save = $this->post( 'save', 'int', 'off' );
        $user_id = $this->post( 'puppetId', 'int' );
        $content = $this->post( 'comment_content', 'int' );
        $target_id = $this->post( 'target_id', 'int' );
        $comment_id = $this->post( 'commentId', 'int', 0 );
        $target_type = $this->post( 'target_type', 'int' );
        $comment_delay = $this->post( 'delay', 'int' );

        if( $comment_id ){
            $cmntStock = sCommentStock::getCommentByStockId( $this->_uid, $comment_id );
            $content = $cmntStock->content;
        }
        if( !$content ){
            return error('EMPTY_COMMENT');
        }

        $comment_delay = Carbon::now()->addSeconds($comment_delay);
        Queue::later( $comment_delay, new PuppetComment( $user_id, $content, $target_type, $target_id ));
        //$comment = sComment::addNewComment( $user_id, $content, $target_type, $target_id );

        if( $save == 'on' ){
            $cmntStock = sCommentStock::addComments( $this->_uid, [$content] );
            $comment_id = $cmntStock->id;
        }
        if( $comment_id ){
            $cmntStock = sCommentStock::usedComment( $comment_id );
        }


        return $this->output_json( ['result'=>'ok'] );
    }

}
