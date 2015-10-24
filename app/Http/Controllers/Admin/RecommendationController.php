<?php namespace App\Http\Controllers\Admin;

use App\Services\User as sUser;
use App\Services\Usermeta as sUsermeta;
use App\Services\UserRole as sUserRole;
use App\Services\UserLanding as sUserLanding;
use Html;

class RecommendationController extends ControllerBase
{
	public function indexAction(){
		return $this->output();
	}

	public function list_usersAction(){
		$role = $this->get('role','int', 0);

  		$user = new sUser;
        $cond = array();

        $users = sUserRole::getUsersByIds( $role );
        foreach($users as $row){
            $uid = $row->uid;
            $row->sex = get_sex_name($row->sex);
            $row->avatar = $row->avatar ? '<img class="user-portrait" src="'.$row->avatar.'" />':'无头像';
            $row->reigster_time = date('Y-m-d H:i', $row->create_time);

            $time = sUsermeta::read_user_forbid($uid);
            if($time != -1 and ($time == "" || $time < time())) {
                $row->oper = Html::link('#', '通过', array(
                    'data'=>-1,
                    'uid' => $uid,
                    'class'=>'accept'
                ));
                $row->oper += Html::link('#', '拒绝', array(
                    'data'=>0,
                    'uid' => $uid,
                    'class'=>'reject'
                ));
            }


            $row->user_landing = '';
            $user_landings = array();
            sUserLanding::getUserLandings($row->uid, $user_landings);

            foreach($user_landings as $key=>$val) {
                if(!$val) continue;
                switch($key) {
                case 'weixin':
                    break;
                case 'weibo':
                    $row->user_landing .= ' <a target="_blank" href="http://weibo.com/'.$val.'" />微博</a>';
                    break;
                case 'QQ':
                    break;
                }
            }
        }
        $data = [
        	'data'=> $users,
        	'recordTotal'=>count($users)
        ];
        // 输出json
        return $this->output_table($data);
	}

}
