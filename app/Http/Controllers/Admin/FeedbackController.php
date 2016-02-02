<?php namespace App\Http\Controllers\Admin;

use App\Models\Feedback as mFeedback,
    App\Models\User as mUser;

use App\Services\ActionLog;
use App\Services\Feedback as sFeedback;
use App\Services\Usermeta as sUsermeta;

use Request;

class FeedbackController extends ControllerBase{
    public function indexAction(){
        sUsermeta::writeUserMeta( $this->_uid, 'last_read_feedback_time', time() );

        return $this->output();
    }

    public function list_fbAction(){
        $fbModel = new mFeedback;
        $user    = new mUser;

        $cond = array();
        $uid = $this -> post('uid', 'int');
        $status = $this -> get('status', 'string');
        switch($status){
            case 'suspend':
                $cond[$fbModel->getTable().'.status']=mFeedback::STATUS_SUSPEND;
                break;
            case 'following':
                $cond[$fbModel->getTable().'.status']=mFeedback::STATUS_FOLLOWED;
                break;
            //default:
                // $cond[get_class($fbModel).'.status']=array( Feedback::STATUS_DELETED, '!=');
        }
        $cond[$user->getTable().'.uid'] = $uid;
        $cond[$user->getTable().'.username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );
        $cond[$user->getTable().'.nickname']   = array(
            $this->post("nickname", "string"),
            "LIKE",
            "AND"
        );

        $join = array();
        $join['User'] = 'uid';

        $bigmen = $this->page( $fbModel, $cond, $join );
        foreach ($bigmen['data'] as $bigman) {
            $bigman->create_time = date('Y-m-d H:i:s', $bigman->create_time);
            $bigman->del_time = date('Y-m-d H:i:s', $bigman->del_time);
            $bigman->avatar = '<img class="user-portrait" src='.$bigman->avatar.' alt="头像">';
            $opinions = json_decode($bigman->opinion);
            $opns = array();
            foreach( $opinions as $opinion ){
                $str  = '<li class="opinion_item">';
                $str .= $opinion->username.' '.date('Y-m-d H:i:s', $opinion->comment_time).'<br />';
                $str .= '·'.$opinion->opinion;
                $str .= '</li>';
                $opns[] = $str;
            }
            $opnBox = '<ul class="opinion_list">'.implode('', $opns).'</ul>';
            if( $bigman->status == mFeedback::STATUS_FOLLOWED || $bigman->status == mFeedback::STATUS_SUSPEND ){
                $opnBox .='<div name="post_opinion"><input type="hidden" name="fbid" value="'.$bigman->id.'"/><input type="text" name="opinion" class="opinion" placeholder="请填写备注" /><input type="button" class="submit_opinion" value="提交" /></div>';
            }
            else{
                $opnBox .= '<div name="post_opinion"><b>待处理 或 已跟进 时才能填写记录。</b></div>';
            }

            $bigman->opinion = $opnBox;
            $bigman->sex = get_sex_name($bigman->sex);

            $bigman->crnt_status = sFeedback::getStatusName( $bigman->status );
            $bigman->oper = $this-> get_next_oper($bigman->status);
        }

        return $this->output_table($bigmen);
    }

    public function chg_statusAction(){
        if( !Request::ajax() ){
            return error('WRONG_ARGUMENTS');
        }

        $fb_id = $this->post('fb_id', 'int');
        $status = $this->post('status', 'string');
        if( empty($fb_id) ){
            return error('EMPTY_ID');
        }

        if( !array_key_exists($status, mFeedback::$status_name) ){
            return error('EMPTY_STATUS');
        }

        sFeedback::changeStatusTo($fb_id, $status, $this->_uid);

        return $this->output();
    }

    protected function get_next_oper( $current_status ){
        //dump($current_status);
        if(!array_key_exists($current_status, mFeedback::$status_name)){
            return '';
        }

        $next_status = mFeedback::$next_status[$current_status];

        $opers = array(
            $this->oper_button( $next_status )
        );

        if($current_status == mFeedback::STATUS_SUSPEND){
            $opers[] = $this -> oper_button( mFeedback::STATUS_REJECTED );
        }

        return implode( ' ', $opers );
    }

    protected function oper_button( $status ){
        $oper_name = array(
            'DELETED'  => '删除',
            'SUSPEND'  => '恢复',
            'FOLLOWED' => '跟进',
            'RESOLVED' => '解决',
            'REJECTED' => '拒绝'
        );

        if( !array_key_exists($status, $oper_name)){
            return '无';
        }

        return '<a href="#" class="chg_status" data-next-status="'.$status.'">'.$oper_name[$status].'</a>';
    }

    public function post_opinionAction(){
        $uid = $this->_uid;
        $fbid = $this->post('fbid','int');
        if( !$fbid ){
            return error( 'EMPTY_FEEDBACK_ID' );
        }
        $opinion = $this->post('opinion', 'string');
        if( !$opinion ){
            return error( 'EMPTY_OPINION' );
        }

        sFeedback::postOpinion($fbid, $uid, $opinion);

        return $this->output_json(['result'=>'ok']);
    }
}
