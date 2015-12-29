<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Count;
use App\Models\Usermeta;
use App\Models\Ask;
use App\Models\Master;
use App\Models\ActionLog;
use App\Models\Role as mRole;

use App\Services\Usermeta as sUsermeta,
    App\Services\Ask as sAsk,
    App\Services\User as sUser,
    App\Services\Reply as sReply,
    App\Services\Inform as sInform,
    App\Services\Follow as sFollow,
    App\Services\Device as sDevice,
    App\Services\Role as sRole,
    App\Services\UserRole as sUserRole,
    App\Services\UserLanding as sUserLanding,
    App\Services\UserDevice as sUserDevice,
    App\Services\Recommendation as sRec,
    App\Services\Download as sDownload;

use App\Counters\AskDownloads as cAskDownloads,
    App\Counters\AskReplies as cAskReplies,
    App\Counters\UserDownloadAsks as cUserDownloadAsks,
    App\Counters\UserReplies as cUserReplies,
    App\Counters\UserAsks as cUserAsks;

use Request, Html, Form, Carbon\Carbon;

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
        $cond['users.uid']        = $this->post("uid", "int");
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
        $start_time = $this->post("start_time", "string");
        if( $start_time ){
            $start_time = Carbon::createFromFormat('Y-m-d H:i', $start_time)->timestamp;
            $cond['users.create_time'] = [ $start_time, '>' ];
        }
        $end_time = $this->post("end_time", "string");
        if( $end_time ){
            $end_time = Carbon::createFromFormat('Y-m-d H:i', $end_time)->timestamp;
            $cond['users.create_time'] = [ $end_time, '<' ];
        }
        if( $start_time && $end_time ){
            $cond['users.create_time']   = array(
                [ $start_time, $end_time ],
                "BETWEEN"
            );
        }

        $join = [];
        $pc_host = env('MAIN_HOST');

        $data  = $this->page($user, $cond, $join, ['uid DESC']);
        foreach($data['data'] as $row){
            $uid = $row->uid;
            $row->sex = get_sex_name($row->sex);
            $row->avatar = $row->avatar ? '<img class="user-portrait" src="'.$row->avatar.'" /></a>':'无头像';
            $row->nickname = '<a href="http://'.$pc_host.'/#homepage/reply/'.$uid.'" target="_blank">'.$row->nickname.'</a>';
            $row->create_time = date('Y-m-d H:i', $row->create_time);
            $row->last_login_time = date('Y-m-d H:i', $row->last_login_time);

            $row->download_count    = cUserDownloadAsks::get($uid);
            $row->asks_count        = cUserAsks::get($uid);
            $row->replies_count     = cUserReplies::get($uid);
            $row->inprogress_count  = cUserDownloadAsks::get($uid, 'processing');

            // $row->upload_count        = 0;
            // $row->total_inform_count  = sInform::countReportedTimesByUid( $uid );
            // $row->share_count         = 0;
            // $row->wxshare_count       = 0;
            // $row->friend_share_count  ="辣么任性";
            // $row->comment_count       = 0;
            // $row->focus_count         = 0;

            $time = sUsermeta::read_user_forbid($uid);
            // if($time != -1 and ($time == "" || $time < time())) {
            //     $row->forbid = Html::link('#', '禁言', array(
            //         'data'=>-1,
            //         'uid' => $uid,
            //         'class'=>'forbid'
            //     ));
            // }
            // else {
            //     $row->forbid = Html::link('#', '解禁', array(
            //         'data'=>0,
            //         'uid' => $uid,
            //         'class'=>'forbid'
            //     ));
            // }

            $setRoleList = sRole::getRoles( [mRole::ROLE_NEWBIE, mRole::ROLE_GENERAL, mRole::ROLE_TRUSTABLE] )->toArray();
            $setRoleIds = array_column( $setRoleList, 'id' );
            $setRoleNames = array_column( $setRoleList, 'display_name' );
            $user_role_ids= array_column( sUserRole::getRolesByUid( $row->uid ), 'id' );
            $opt = ['multiple'=>'multiple','class' => 'form-control'];
            if( $row->status < 0){
                $opt['disabled'] = 'disabled';
                $block_btn = '<span class="btn btn-info chg_user_stat" data-status="'.$row->status.'">取消屏蔽用户</span>';
            }
            else{
                $block_btn = '<span class="btn btn-danger chg_user_stat" data-status="'.$row->status.'">屏蔽用户</span>';
            }
            $userRoleList = Form::select('user-roles', array_combine( $setRoleIds, $setRoleNames ), $user_role_ids, $opt );

            $recRole = sRec::getRecRoleIdByUid( $row->uid );
            $recRoleList = sRole::getRoles( [mRole::ROLE_STAR, mRole::ROLE_BLACKLIST] )->toArray();
            $recRoleName = array_column( $recRoleList, 'display_name' );
            array_walk( $recRoleName, function(&$value) {
                $value = $value.'推荐';
            });
            $recRoleList = array_combine( array_column( $recRoleList, 'id'), $recRoleName );
            $recRoleList[''] ='未推荐';
            $opt = ['class' => 'form-control'];
            if( $recRole ){
                $opt = ['disabled'=>'disabled'];
                $recReason = '';
            }
            else{
                $recReason = '<input type="text" name="reason" placeholder="推荐理由"/>
                            <input type="button" name="recommend" class="recommend" value="推荐"/>';
            }
            $recList = Form::select('recommend-roles', $recRoleList, $recRole, $opt);
            $row->oper   = '<div>'.$userRoleList.'</div><div>'.$recList.$recReason.'</div>';
            $row->assign = Html::link('#assign_role', '赋予角色', array(
                'data-toggle'=>'modal',
                'class'=>'assign',
                'data-uid'=>$uid
            ));
            $master_oper_name = ($row->is_god==0)?'设置':'取消';
            $row->master = Html::link('#', $master_oper_name, array(
                'class'=>'master',
                'data-uid'=>$uid,
                'data-isgod' => $row->is_god
            ));

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

            $row->device = '-';

            $deviceRec = sUserDevice::getUserDeviceId( $uid );
            if( $deviceRec ){
                $device = sDevice::getDeviceById($deviceRec);
                if( !$device ){
                    $row->device = '设备已失效';
                }
                else{
                    $dev_arr = sDevice::humanReadableInfo( $device );
                    $dev_str = [];
                    foreach( $dev_arr as $key => $value){
                        $dev_str[]= $key.': '.$value;
                    }
                    $row->device = '<div class="device_box">'.implode('</div><div class="device_box">', $dev_str ).'</div>';
                }
            }
            else{
                $row->device = 'PC';
            }
        }
        // 输出json
        return $this->output_table($data);
    }

    public function set_masterAction(){
        $uid = $this->post('uid', 'int');
        $status = $this->post('status', 'int', 1);

        if( !$uid ){
            return error( 'EMPTY_UID' );
        }


        $user = sUser::setMaster( $uid, !$status );
        return $this->output( ['result'=>'ok'] );
    }

}
