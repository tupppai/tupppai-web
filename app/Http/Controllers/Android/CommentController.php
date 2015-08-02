<?php
namespace Psgod\Android\Controllers;

use Psgod\Models\ActionLog;
use Psgod\Models\User;
use Psgod\Models\Vote;
use Psgod\Models\Record;
use Psgod\Models\Count;
use Psgod\Models\Message;
use Psgod\Models\Reply;
use Psgod\Models\Ask;
use Psgod\Android\Models\Comment;


use Psgod\Services\Comment as sComment;

class CommentController extends ControllerBase
{
	public function indexAction()
    {
        $type       = $this->get('type', 'int', Comment::TYPE_ASK);
        $target_id  = $this->get('target_id', 'int');
        $page       = $this->get('page', 'int', 1);
        $size       = $this->get('size', 'int', 10);
        if(!$target_id){
            return ajax_return(1, '请选择ID！');
        }

        $data = sComment::getComments($type, $target_id, $page, $size);
        return $this->output($data);
	}

    /**
     * 添加评论
     * $return integer  新增评论
     */
    //public function send_commentAction() {
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
        return $this->output($ret);
    }
    
    public function upCommentAction($id) {
        $status = $this->get('status', 'int', 1);

        $ret    = sComment::updateCommentCount($id, 'up', $status);
        return $this->output();
    }

    public function informCommentAction($id) {
        $status = $this->get('status', 'int', 1);

        $ret    = sComment::updateCommentCount($id, 'inform', $status);
        return $this->output();
    } 
}
