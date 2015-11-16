<?php namespace App\Http\Controllers\Admin;

use App\Models\Comment as mComment;
use App\Models\User as mUser;

use App\Services\Comment as sComment;
use App\Services\Reply as sReply;
use App\Services\User as sUser;
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
        $comment = new mComment;
        $cond[$comment->getTable().'.uid'] = $this->post('uid');
        $cond[(new mUser)->getTable().'.nickname']   = array(
            $this->post("nickname", "string"),
            "LIKE",
            "AND"
        );
        $cond['content'] = array(
            $this->post("content", "string"),
            "LIKE",
            "AND"
        );

        $status = $this->get("status", "string");
        switch( $status ){
            case 'blocked':
                $status = mComment::STATUS_BLOCKED;
                break;
            case 'deleted':
                $status = mComment::STATUS_DELETED;
                break;
            case 'all':
            default:
                $status = NULL;
        }
        $cond[$comment->getTable().'.status'] = $status;

        $join = array();
        $join['User'] = 'uid';

        $data  = $this->page($comment, $cond, $join);
        $hostname = env('MAIN_HOST');
        foreach($data['data'] as $row){
            $row->id =  $row->id;
            $user = sUser::getUserByUid( $row->uid );
            $row->user = '<a href="http://'.$hostname.'/home.html#home/ask/' . $row->uid . '" target="_blank">'.$user->nickname.'</a>(uid:'.$user->uid.')';
            $url = '';
            if( $row->type == mComment::TYPE_ASK ){
                $url = 'http://'.$hostname.'/#comment/ask/'.$row->target_id;
            }
            else if( $row->type == mComment::TYPE_REPLY ){
                $url = 'http://'.$hostname.'/#comment/reply/'.$row->target_id;
            }
            else{
                $url = '';
            }
            $row->original = '<a href="'.$url.'">'.$row->content.'</a>';


            $row->create_time = date('Y-m-d H:i:s', $row->create_time );
            $oper = [];
            if( $row->status > mComment::STATUS_DELETED ){
                $oper[] = "<a class='update_status' data-status='-1'>屏蔽</a>";
            }
            else if( $row->status == mComment::STATUS_BLOCKED ){
                $oper[] = "<a class='update_status' data-status='1'>取消屏蔽</a>";
            }
            else{
                //$row->oper[] = [];
            }


            if( $row->status == mComment::STATUS_DELETED ){
                $oper[] = "<a class='update_status' data-status='1'>恢复</a>";
            }
            else{
                $oper[] = "<a class='update_status' data-status='0'>删除</a>";
            }

            $row->oper = implode( ' ', $oper );
        }
        // 输出json
        return $this->output_table($data);
    }


    public function update_statusAction()
    {
        $id = $this->post("id", "int");
        if( !$id ){
            return error('EMPTY_COMMENT');
        }

        $status = $this->post('status', 'int' );

        switch( $status ){
            case 0:
                sComment::deleteComment( $id );
                break;
            case 1:
                sComment::restoreComment( $id );
                break;
            case -1:
                sComment::blockComment( $id );
                break;
            default:
                break;
        }

        return $this->output_json(['result'=>'ok']);
    }

    public function send_commentAction(){
        $save = $this->post( 'save', 'int', 'off' );
        $user_id = $this->post( 'puppetId', 'int' );
        $content = $this->post( 'comment_content', 'string' );
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
