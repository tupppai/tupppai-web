<?php namespace App\Http\Controllers\Admin;

use App\Models\Role as mRole,
    App\Models\User as mUser,
    App\Models\UserScheduling as mUserScheduling,
    App\Models\UserScore as mUserScore,
    App\Models\Usermeta as mUsermeta;

use App\Services\UserRole as sUserRole,
    App\Services\Role as sRole,
    App\Services\Follow as sFollow,
    App\Services\Ask as sAsk,
    App\Services\User as sUser,
    App\Services\Config as sConfig,
    App\Services\userLanding as sUserLanding,
    App\Services\ActionLog as sActionLog,
    App\Services\UserScore as sUserScore,
    App\Services\Usermeta as sUsermeta,
    App\Services\UserScheduling as sUserScheduling,
    App\Services\UserSettlement as sUserSettlement;

use App\Counters\UserAsks as cUserAsks,
    App\Counters\UserInforms as cUserInforms;

use Request, Html, Form;

class WaistcoatController extends ControllerBase
{

    public function initialize() {
        $request = $this->request;

        view()->share('roles', sRole::getRoles());
        view()->share('role_name', $request::segment(2));
    }

    public function indexAction() {
        return $this->output();
    }

    public function helpAction() {
        return $this->output();
    }

    public function workAction() {
        return $this->output();
    }

    public function parttimeAction() {
        $num    = sUserRole::countRolesById(mRole::TYPE_PARTTIME);
        $score  = sUserSettlement::sumTotalScore();

        return $this->output(array(
            'num' => $num,
            'score' => round($score)
        ));
    }

    public function staffAction() {
        $rate   = sConfig::getConfig(mUsermeta::KEY_STAFF_TIME_PRICE_RATE);
        $num    = sUserRole::countRolesById(mRole::TYPE_PARTTIME);
        $score  = sUserSettlement::sumTotalScore();

        return $this->output(array(
            'rate'=>$rate,
            'num' => $num,
            'score' => round($score)
        ));
    }

    public function juniorAction() {

        return $this->output();
    }

    public function list_usersAction()
    {
        $user = new mUser;
        $pc_host = env('MAIN_HOST');

        // 检索条件
        $cond = array();
        $cond['role_id']    = $this->get("role_id", "int");
        $cond[$user->getTable().'.uid'] = $this->post("uid", "int");
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
         // 关联表数据结构
        $join = array();
        $join['UserRole'] = 'uid';

        $order = 'id DESC';
        $types = sUserScheduling::operTypes();

        // 用于遍历修改数据
        $data  = $this->page($user, $cond, $join, $order );

        foreach($data['data'] as $row){
            $row->uid = $row->uid;
            // 兼职员工的换算单位
            // 后台账号的兑换比例
            $row->rate = 1;
            if($cond['role_id'] == mRole::TYPE_STAFF){
                $default_rate   = sConfig::getConfig(mUsermeta::KEY_STAFF_TIME_PRICE_RATE);
                $row->rate = sUsermeta::get( $row->uid, mUsermeta::KEY_STAFF_TIME_PRICE_RATE, $default_rate );
            }
            // 结算金额
            $row->paid_money    = sprintf('%0.1f', sUserSettlement::sumTotalScore($row->uid));
            //const STATUS_NORMAL = 0;
            //const STATUS_PAID   = 1;
            //const STATUS_COMPLAIN = 2;
            //const STATUS_DELETED  = 3;
            // 兼职结算的分数
            $balance = sUserScore::getBalance($row->uid);
            $row->current_score = $balance[mUserScore::STATUS_NORMAL];
            $row->paid_score    = $balance[mUserScore::STATUS_PAID];
            $row->total_score   = $row->current_score + $row->paid_score;
            // 员工结算的时间，按天
            //$balance = sUserScheduling::getBalance($row->uid);
            $balance = sUserScore::getBalance($row->uid); //Which one?
            $row->current_time  = $balance[mUserScheduling::STATUS_NORMAL];
            $row->paid_time     = $balance[mUserScheduling::STATUS_PAID];
            $row->total_time    = $row->current_time + $row->paid_time;
            // 按小时结算
            $row->current_hour  = ($row->current_time);
            $row->paid_hour     = ($row->paid_time);
            $row->total_hour    = ($row->total_time);
            // 按天结算
            $row->current_day   = ($row->current_time);
            $row->paid_day      = ($row->paid_time);
            $row->total_day     = ($row->total_time);
            // 换算薪资
            $row->hour_money    = number_format( $row->current_time*$row->rate , 1);

            $row->create_time = date("Y-m-d H:i", $row->create_time);

            //$row->sex = get_sex_name($row->sex);

            $row->ask_count     = cUserAsks::get($row->uid);
            $row->inform_count  = cUserInforms::get($row->uid);

            $row->remark        = sUsermeta::read_user_remark($row->uid);

            //todo: count
            $row->passed_replies_count  = sUserScore::countPassedReplies($row->uid);
            $row->rejected_replies_count= sUserScore::countRejectedReplies($row->uid);
            $row->total_replies_count = $row->passed_replies_count + $row->rejected_replies_count;


            $row->total_score   = sUserScore::sumOperUserScore($row->uid);
            //平均审分
            $row->avg_score     = sUserScore::avgOperUserScore($row->uid);
            //平均得分
            $row->avg_points    = sUserScore::avgUserScore($row->uid);
            //$row->avg_score = number_format($avg_score,1);

            $logs   = sActionLog::get_log($row->uid);
            foreach($types as $key=>$type){
                if(!isset($type_arr[$key])) $row->$key = 0;
                foreach($logs as $index=>$val) {
                    if(in_array($index, $type)){
                        $row->$key += $val;
                    }
                }
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

            $row->rate = Form::input('text', 'rate', $row->rate, array(
                'class'=>'form-control'
            ));
            $row->rate .= Form::button('保存', array(
                'data'=>$row->uid,
                'type'=>'submit',
                'class'=>'form-control rate_save'
            ));
            $row->data = Html::link('#remark_user', '备注', array(
                'data-toggle'=>'modal',
                'class'=>'remark',
                'remark'=>$row->remark,
                'uid'=>$row->uid,
                'nickname'=>$row->nickname
            ));
            $row->data .= Html::link("http://$pc_host/user/profile".$row->uid, '详情', array(
                'class'=>'detail',
                'target'=>'_blank'
            ));
            $row->avatar = Html::image($row->avatar, 'avatar', array(
                'class'=>'user-portrait'
            ));
            $row->money  = Html::link('#', ' 结算资金 ', array(
                'class'=>'paid',
                'uid' => $row->uid
            ));
            $row->money .= Html::link('#', ' 结算记录 ', array(
                'class'=>'paid_list',
                'uid' => $row->uid
            ));
            $row->set_time = Html::link('#add_user_schedule', '设置', array(
                'style'=>'color:green',
                'data-toggle'=>'modal',
                'class'=>'set_time',
                'uid'=>$row->uid,
                'nickname'=>$row->nickname
            ));
            $row->set_time .= Html::link('/scheduling/index?uid='.$row->uid, '查看');
        }

        // 输出json
        return $this->output_table($data);
    }

    public function create_userAction() {
        $username = $this->post("username", "string");
        $password = $this->post("password", "string");
        $nickname = $this->post("nickname", "string");
        $sex      = $this->post("sex", "int");
        $phone    = 19000000000;//$this->post("phone", "int");
        $avatar   = $this->post("avatar", "string");
        $role_id  = $this->post("role_id", "int");

        if(is_null($username) || is_null($nickname)){
            return error('EMPTY_USERNAME', '请输入角色名称或展示名称');
        }
        if( is_null($password) ){
            return error('EMPTY_PASSWORD', '请输入角色名称或展示名称');
        }
        if( is_null($sex) ){
            return error('EMPTY_SEX' , '请输入角色名称或展示名称' );
        }
        //保存事件
        fire('BACKEND_HANDLE_ADDWAISTCOATUSER',[
            $username,
            $password, $nickname,
            $sex,
            $phone,
            $avatar,
            $role_id]
        );
        return $this->output_json(['result' => 'ok']);
    }

    public function remarkAction(){
        $id         = $this->post('id');
        $nick       = $this->post('name');
        $password   = $this->post('password');
        $is_reset   = $this->post('is_reset');
        $remark     = trim($this->post('remark'));

        if(is_null($id)){
            return error( 'EMPTY_UID', '参数错误' );
        }
        sUser::setRemarkForUser( $id, $nick, $password, $is_reset, $remark );
        return $this->output_json(['result'=>'ok']);
    }

    public function set_timeAction(){
        if( !$this->request->isAjax() ){
            return array();
        }

        $uid = $this->post('uid', 'int', 0);
        $start_time = $this->post('start_time', 'int', 0);
        $end_time = $this->post('end_time', 'int', 0);
        if( $start_time < time() ){
            return ajax_return(5, '不能设置过去的时间');
        }
        if( $start_time > $end_time ){
            return ajax_return(4,'开始时间不能晚于结束时间');
        }
        if( ($end_time - $start_time) - 24 * 60 * 60 > 0 ) {
            return ajax_return(4,'工作时间不能超过12h');
        }

        $schedule = new UserScheduling();
        $schedule->uid = $uid;
        $schedule->start_time = $start_time;
        $schedule->end_time = $end_time;
        $schedule->set_by = $this->_uid;
        $schedule->del_by = 0;
        $schedule->del_time = 0;
        $schedule->create_time = time();
        $schedule->update_time = time();
        $schedule->status = UserScheduling::STATUS_NORMAL;

        $data = $schedule->save_and_return($schedule);
        if( $data ){
            ActionLog::log(ActionLog::TYPE_SET_STAFF_TIME, array(), $data);
            return ajax_return(1,'添加成功');
        }
        else{
            return ajax_return(2,'添加失败');
        }
    }
}
