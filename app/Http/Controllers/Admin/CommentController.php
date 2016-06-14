<?php namespace App\Http\Controllers\Admin;

use App\Models\Comment as mComment;
use App\Models\Puppet as mPuppet;
use App\Models\User as mUser;

use App\Services\Comment as sComment;
use App\Services\Reply as sReply;
use App\Services\User as sUser;
use App\Services\Puppet as sPuppet;
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
        $statCol = $comment->getTable().'.status';
        switch( $status ){
            case 'blocked':
                $cond[$statCol] = mComment::STATUS_BLOCKED;
                break;
            case 'deleted':
                $cond[$statCol] = mComment::STATUS_DELETED;
                break;
            case 'all':
                $status = $cond[$statCol] = array(
                            implode(',', [
                                mComment::STATUS_BLOCKED,
                                mComment::STATUS_DELETED
                            ]),
                            "NOT IN"
                        );
            default:
                $status = NULL;
        }

        $join = array();
        $join['User'] = 'uid';

        $data  = $this->page($comment, $cond, $join);
        $hostname = env('MAIN_HOST');
        foreach($data['data'] as $row){
            $row->id =  $row->id;
            $user = sUser::getUserByUid( $row->uid );
            $row->user = '<a href="http://'.$hostname.'/#homepage/reply/' . $row->uid . '" target="_blank">'.$user->nickname.'</a>(uid:'.$user->uid.')';
            $url = '';
            if( $row->type == mComment::TYPE_ASK ){
                $url = 'http://'.$hostname.'/#askdetail/ask/'.$row->target_id;
            }
            else if( $row->type == mComment::TYPE_REPLY ){
                $url = 'http://'.$hostname.'/#replydetailplay/0/'.$row->target_id;
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
        $comment_mode = $this->post( 'comment_mode', 'string', 'single_comment' );
        $comment_amount = $this->post( 'comment_amount', 'int', 1 );

        if( $comment_mode == 'single_comment' ){
            if( $save == 'on' ){
                $cmntStock = sCommentStock::addComments( $this->_uid, [$content] );
                $comment_id = $cmntStock->id;
            }

            if( $comment_id ){
                $cmntStock = sCommentStock::getCommentByStockId( $this->_uid, $comment_id );
                $content = $cmntStock->content;
            }
            $comment_uids = [$user_id];
            $contents  = [$content];
            $comment_ids = [0];
        }
        else{
            $comments = sCommentStock::getComments( $this->_uid )->toArray();
            $roles = [ mPuppet::ROLE_CRITIC ];
            $puppets = sPuppet::getPuppets( $this->_uid, $roles );
            $comment_amount = mt_rand( $comment_amount-3, $comment_amount+3 );
            $comment_amount = min( count($puppets), count($comments), $comment_amount );

            shuffle( $puppets );
            $comment_puppets = array_slice( $puppets, 0, $comment_amount );
            $comment_uids = array_column( $comment_puppets, 'uid' );

            shuffle( $comments );
            $comment_contents = array_slice( $comments, 0, $comment_amount );
            $contents = array_column( $comment_contents, 'content' );
            $comment_ids = array_column( $comment_contents, 'id' );
        }

        $comment_delay = Carbon::now()->addSeconds($comment_delay);

        foreach( $comment_uids as $key => $uid ){
            $content = $contents[$key];
            Queue::later( $comment_delay, new PuppetComment( $uid, $content, $target_type, $target_id ));

            $comment_id = $comment_ids[$key];
            $cmntStock = sCommentStock::usedComment( $comment_id );
        }

        return $this->output_json( ['result'=>'ok'] );
    }

}
