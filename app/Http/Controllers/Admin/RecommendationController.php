<?php namespace App\Http\Controllers\Admin;

use App\Services\User as sUser;
use App\Services\Usermeta as sUsermeta;
use App\Services\UserRole as sUserRole;
use App\Services\Recommendation as sRec;
use App\Services\UserLanding as sUserLanding;
use App\Services\Recommendation as sRecommendation;

class RecommendationController extends ControllerBase
{
	public function indexAction(){
		return $this->output();
	}

	public function list_usersAction(){
        $role = $this->get('role','int');
		$type = $this->get('type','string', 0);


  		$user = new sUser;
        $cond = array();
        if( $type == "unreviewed" ){
            $users = sRec::getCheckedRecByRoleId( $role );
        }
        else if( $type == "invalid" ){
            $users = sRec::getInvalidRecByRoleId( $role );
        }
        else if( $type == "rejected" ){
            $users = sRec::getRejectedRecByRoleId( $role );
        }
        else if( $type == "pending"){
            $users = sRec::getPendingRecByRoleId( $role );
        }
        else{
            $users = sRec::getPassedRecByRoleId( $role );
        }

        $arr =[];
        foreach($users as $row){
            $uid = $row->uid;
            $row->checkbox = '<input type="checkbox" name="check_user" />';
            $row->nickname = $row->user->nickname;
            $row->register_time = date( 'Y-m-d H:i', $row->user->create_time );
            $row->sex = get_sex_name($row->sex);
            $row->avatar = $row->user->avatar ? '<img class="user-portrait" src="'.$row->user->avatar.'" />':'无头像';
            $row->recommend_time = date('Y-m-d H:i', $row->create_time);

            $row->user_landing = 'None';
            $row->introducer_name = $row->introducer->username;
            if( !$row->result ){
                $row->result = '(未审核)';
            }
            $user_landings = array();
            sUserLanding::getUserLandings($row->uid, $user_landings);

            foreach($user_landings as $key=>$val) {
                if(!$val) continue;
                switch($key) {
                    case 'weibo':
                        $row->user_landing .= ' <a target="_blank" href="http://weibo.com/'.$val.'" />微博</a>';
                        break;
                    case 'weixin':
                    case 'QQ':
                    default:
                        $row->user_landing = 'None';
                }
            }
            $arr[] = $row;
        }

        $data = [
            'data'=> $arr,
            'recordTotal'=>count($arr)
        ];
        // 输出json
        return $this->output_table($data);
	}

    public function userAction(){
        $uid = $this->post('uid', 'int');
        $reason = $this->post('reason', 'string');
        $role_id = $this->post('role_id', 'int');

        if( !$reason ){
            return error('EMPTY_REASON');
        }

        if( !$uid ){
            return error('EMPTY_UID');
        }

        if( !$role_id ){
            return error('EMPTY_ROLE_ID');
        }
        sRecommendation::addNewRec( $this->_uid, $uid, $role_id, $reason );

        return $this->output_json(['result'=>'ok']);
    }

    public function chg_statAction(){
        $ids = $this->post('ids', 'string' );
        $status = $this->post('status', 'string' );

        if( is_null($status) ){
            return error('EMPTY_STATUS');
        }

        sRecommendation::updateStatus( $this->_uid, $ids, $status );

        return $this->output_json(['result'=>'ok']);
    }
}
