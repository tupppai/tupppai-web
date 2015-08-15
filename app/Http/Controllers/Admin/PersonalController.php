<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Count;
use App\Models\Usermeta;
use App\Models\Ask;
use App\Models\Master;
use App\Models\ActionLog;

use App\Services\Usermeta as sUsermeta,
    App\Services\Ask as sAsk,
    App\Services\User as sUser,
    App\Services\Reply as sReply,
    App\Services\Follow as sFollow,
    App\Services\Download as sDownload;

use Request, Html;

class PersonalController extends ControllerBase
{

    public function indexAction()
    {
        return $this->output();
    }

    public function created_userAction()
    {
        return $this->output();
    }

    public function list_created_usersAction(){
        $actionLog = new ActionLog();
        $ownerUid = $this->get('creator','int', $this->_uid);

        $created_user = $actionLog->get_log_by_uid_and_oper_type( $ownerUid, ActionLog::TYPE_REGISTER )->toArray();
        $usersDiff = array_column( $created_user, 'data');
        array_walk( $usersDiff, function( &$value ){
            $value = json_decode($value, true);
        });
        $uids = array_column( $usersDiff, 'uid' );

        $user = new User;
        $cond = array();
        $findUid = $this->post("uid", "int");
        if( in_array( $findUid, $uids ) ){
            $cond['uid'] = array( $findUid, 'IN');
        }
        else{
            $cond['uid'] = array( implode(',',$uids), 'IN' );
        }

        $cond['username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );
        $cond['nickname']   = array(
            $this->post("nickname", "string"),
            "LIKE",
            "AND"
        );
        $cond['phone']   = array(
            $this->post("phone", "string"),
            "LIKE",
            "AND"
        );

        $_REQUEST['sort'] = "create_time desc";

        $data  = $this->page($user, $cond, array(), 'uid DESC');
        foreach($data['data'] as $row){
            $uid = $row->uid;
            $row->sex = get_sex_name($row->sex);
            $row->avatar = $row->avatar ? '<img class="user-portrait" src="'.$row->avatar.'" />':'无头像';
            $row->create_time = date('Y-m-d H:i', $row->create_time);
            $creator = User::findUserByUID($ownerUid);
            $row->creator = $creator->username;

        }
        // 输出json
        return $this->output_table($data);
    }

    public function list_usersAction(){
    	$user = new User;
        $cond = array();
        $cond['uid']        = $this->post("uid", "int");
        $cond['username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );
        $cond['nickname']   = array(
            $this->post("nickname", "string"),
            "LIKE",
            "AND"
        );

        $_REQUEST['sort'] = "create_time desc";

        $data  = $this->page($user, $cond, array(), 'uid DESC');
        foreach($data['data'] as $row){
            $uid = $row->uid;
            $row->sex = get_sex_name($row->sex);
            $row->avatar = $row->avatar ? '<img class="user-portrait" src="'.$row->avatar.'" />':'无头像';
            $row->create_time = date('Y-m-d H:i', $row->create_time);

            $row->download_count    = sDownload::getUserDownloadCount($uid);
            $row->asks_count        = sAsk::getUserAskCount($uid);
            $row->replies_count     = sReply::getUserReplyCount($uid);
            $row->fans_count    = sFollow::getUserFansCount($uid);
            $row->fellow_count  = sFollow::getUserFollowCount($uid);
            /*
            $row->inprogress_count = $user->get_inprogress_count($uid);
            $row->upload_count  =$user->get_upload_count($uid);
            $row->total_inform_count = $user->get_all_inform_count($uid);
            $counts = Count::get_counts_by_uid($uid);
            $row->share_count=$counts[Count::ACTION_SHARE];
            $row->wxshare_count=$counts[Count::ACTION_WEIXIN_SHARE];
            $row->friend_share_count="辣么任性";
            $row->comment_count=$user->get_comment_count($uid);
            $row->focus_count   = $user->get_fellow_count($uid);
             */

            $time = sUsermeta::read_user_forbid($uid);
            if($time != -1 and ($time == "" || $time < time())) {
                $row->forbid = Html::link('#', '禁言', array(
                    'data'=>-1,
                    'class'=>'forbid'
                ));
            }
            else {
                $row->forbid = Html::link('#', '解禁', array(
                    'data'=>0,
                    'class'=>'forbid'
                ));
            }
            $row->oper   = Html::link('#', '编辑', array(
                'class'=>'edit'
            ));
            $row->assign = Html::link('#assign_role', '赋予角色', array(
                'data-toggle'=>'modal',
                'class'=>'assign',
                'data-uid'=>$uid
            ));
            $master_oper_name = ($row->is_god==0)?'设置':'取消';
            $row->master = Html::link('#', $master_oper_name, array(
                'class'=>'master',
                'data-uid'=>$uid
            ));
        }
        // 输出json
        return $this->output_table($data);
    }

    public function set_masterAction(){
        if( !Request::ajax() ){
            return error('SYSTEM_ERROR');
        }

        $uid = $this->post('uid', 'int', 0);
        $user = sUser::getUserByUid($uid);
        if( !$user ){
            return error('USER_NOT_EXIST');
        }

        sUser::setMaster($uid);
        return $this->output();
    }

}
