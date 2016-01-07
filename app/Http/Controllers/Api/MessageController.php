<?php namespace App\Http\Controllers\Api;

use App\Services\ActionLog as sActionLog;
use App\Services\User as sUser;
use App\Services\Label as sLabel;
use App\Services\Count as sCount;
use App\Services\Focus as sFocus;
use App\Services\SysMsg as sSysMsg;
use App\Services\Message as sMessage;

class MessageController extends ControllerBase
{

    public function indexAction() {
        $uid = $this->_uid;
        $page = $this->get('page', 'integer', 1);
        $size = $this->get('size', 'integer', 15);
        $type = $this->get('type', 'string', 'normal');

        $msgs = sMessage::getMessages( $uid, $type, $page, $size );

        return $this->output( $msgs );
    }

    public function listAction(){
        $uid = $this->_uid;
        $type = $this->get('type', 'string', 'comment');
        $page = $this->get('page', 'integer', 1);
        $size = $this->get('size', 'integer', 15);
        $last_updated = $this->get('last_updated', 'integer', time());

        $msgs = sMessage::getMessagesByType( $uid, $type, $page, $size, $last_updated );

        return $this->output( $msgs );
    }

    public function deleteAction(){
        $mids = $this->post('mids', 'string' );
        $type = $this->post('type', 'string');
        $uid = $this->_uid;

        if( $type ){
            sMessage::deleteMessagesByType( $uid, $type );
        }
        else if( $mids ){
            sMessage::deleteMessagesByMessageIds( $uid, $mids );
        }
        else{
            return error('WRONG_ARGUMENTS', '请传参数。');
        }

        return $this->output( true  );
    }

    public function count_unread_noticesAction( ){
        $unread = sMessage::fetchNewMessages( $this->_uid );
        return $this->output( $unread );
    }
}
