<?php
namespace App\Services;
use \App\Services\User as sUser;
use \App\Services\ActionLog as sActionLog;

use \App\Models\Feedback as mFeedback;

class Feedback extends ServiceBase{
    public static function brief( $fb ){
        //temp
        return $fb->toArray( $fb );
    }

    public static function addNewFeedback( $uid, $content, $contact ){
        $fbModel = new mFeedback();
        sActionLog::init( 'ADD_FEEDBACK', $fbModel );
        $fbModel->assign( array(
            'content' => $content,
            'contact' => $contact,
            'uid' => $uid
        ) );
        $fbModel->save();
        sActionLog::save( $fbModel );

        return true;
    }

    public static function getFeedbackById($fbid) {
        return (new mFeedback)->get_feedback_by_fb_id($fbid);
    }

    public static function getStatusName( $status_name ){
        if( !mFeedback::$status_name ) {
            //error?
            return false;
        }
        $status = mFeedback::$status_name;
        if( array_key_exists( $status_name, $status ) ){
            return $status[ $status_name ];
        }
        return false;
    }

    public static function changeStatusTo( $fb_id, $status, $uid ){

        $fbModel = new mFeedback();
        $fb = $fbModel->get_feedback_by_fb_id($fb_id);

        ActionLog::init( 'MODIFY_FEEDBACK_STATUS', $fb );
        $fb->status = $status;
        if( $status == mFeedback::STATUS_DELETED ){
            $fb->del_time = time();
            $fb->del_by   = $uid;
        }
        else{
            $fb->update_time = time();
            $fb->update_by   = $uid;
        }
        $fb->save();
        ActionLog::save( $fb );

        return self::brief( $fb );
    }

    public static function postOpinion( $fbid, $uid, $opinion ){
        $fbModel = new mFeedback();
        $fb = $fbModel->get_feedback_by_fb_id( $fbid );
        if( !$fb ){
            return error( 'FEEDBACK_NOT_EXIST' );
        }
        sActionLog::init( 'ADD_FEEDBACK', $fb );

        $user = sUser::getUserByUid( $uid );
        if( !$user ){
            return error( 'USER_NOT_EXIST' );
        }

        if( $fb->status != mFeedback::STATUS_FOLLOWED && $fb->status != mFeedback::STATUS_SUSPEND ){
            return error( 'STATUS_ERR' );
        }

        $fb->post_opinion( $opinion, $user->username );
        ActionLog::save( json_decode($fb->opinion) );

        return self::brief( $fb );
    }
}
