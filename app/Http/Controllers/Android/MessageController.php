<?php namespace App\Http\Controllers\Android;

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
        $last_updated = $this->get('last_updated', 'integer', time());

        $msgs = sMessage::getMessages( $uid, $page, $size, $last_updated );

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

    public function delMsgAction(){
        $mids = $this->post('mids', 'string', '');
        $type = $this->post('type', 'string','');
        $uid = $this->_uid;

        $res= false;

        if( $type ){
            $msgs = Message::find('msg_type='.$type.' AND receiver='.$uid );
            if( !$msgs ){
                return ajax_return( 1, 'okay', false);
            }
            $mids = implode(',', array_column($msgs-> toArray(), 'id') );
        }
        if( !$mids ){
            return ajax_return(1,'error', false);
        }

        $msgs = Message::find('id IN('.$mids.') AND receiver='.$uid);
        if( !$msgs ){
            return ajax_return( 1, 'okay', false);
        }

        $old = ActionLog::clone_obj($msgs);
        $res = Message::delMsgs( $uid, $mids );
        if( $res ){
            ActionLog::log(ActionLog::TYPE_DELETE_MESSAGES, explode(',',$mids), array());
        }
        return ajax_return(1,'okay', $res);
    }


    public function count_unread_noticesAction( ){
        $unread = sMessage::fetchNewMessages( $this->_uid );
        return $this->output( $unread );
    }
}
