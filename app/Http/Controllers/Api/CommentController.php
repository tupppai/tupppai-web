<?php namespace App\Http\Controllers\Api;

use App\Models\Comment as mComment;
use App\Services\Comment as sComment;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use Queue, App\Jobs\Push;

class CommentController extends ControllerBase
{
    public $_allow = array('index');

	public function indexAction()
    {
        $type       = $this->get('type', 'int', mComment::TYPE_ASK);
        $target_id  = $this->get('target_id', 'int');
        $page       = $this->get('page', 'int', 1);
        $size       = $this->get('size', 'int', 10);

        $comment_id = $this->get('comment_id', 'int');
    
        $need_thread= $this->get('need_photoitem', 'int');

        if( !$target_id ) {
            return error('EMPTY_ID');
        }

        //todo: sky 如果commentid不为空，后续的列表中去掉那个ID
        $data = sComment::getComments($type, $target_id, $page, $size);
        if($need_thread && $need_thread == 1 && $page == 1){
            if($type == mComment::TYPE_ASK){
                $data['thread'] = sAsk::detail(sAsk::getAskById($target_id));
            }
            else if($type == mComment::TYPE_REPLY){
                $data['thread'] = sReply::detail(sReply::getReplyById($target_id));
            }
        }

        if($comment_id && $comment = sComment::getCommentById($comment_id)) {
            //临时解决方案
            foreach($data['new_comments'] as $key=>$row) {
                if($row['comment_id'] == $comment_id)
                    unset($data['new_comments'][$key]);
            }
            array_unshift($data['new_comments'], sComment::detail($comment));
        }
        return $this->output($data);
	}

    /**
     * 添加评论
     * $return integer  新增评论
     */
    public function saveAction() {
        $uid        = $this->_uid;
        $content    = $this->post('content', 'string');
        $type       = $this->post('type', 'int');
        $target_id  = $this->post('target_id', 'int');
        $reply_to   = $this->post('reply_to', 'string', '0');
        $for_comment= $this->post('for_comment', 'int', '0');

        if ( empty($uid) ) {
            return error('USER_NOT_EXIST');
        }

        if ( empty($content) || empty($type) || empty($target_id) ) {
            return error('WRONG_ARGUMENTS');
        }

        $ret = sComment::addNewComment($uid, $content, $type, $target_id, $reply_to, $for_comment);

        return $this->output(['id'=>$ret->id]);
    }
    
    public function upCommentAction($id) {
        $status = $this->get('status', 'int', 1);

        $ret    = sComment::updateCommentCount($id, 'up', $status);

        return $this->output( $ret );
    }

    public function informCommentAction($id) {
        $status = $this->get('status', 'int', 1);

        $ret    = sComment::updateCommentCount($id, 'inform', $status);

        return $this->output( $ret );
    } 
}
