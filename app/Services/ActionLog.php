<?php

namespace App\Services;

use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    \App\Models\ActionLog as mActionLog;

use App\Jobs\ActionLog as jobActionLog;
use Queue;

class ActionLog extends ServiceBase
{
    public static $type = self::TYPE_OTHERS;
    public static $prev = array();
    public static $post = array();

    /**
     * 日志先写在redis后续通过脚本落地
     */
    public static function log($type = self::TYPE_OTHERS, $old_obj = array(), $new_obj = array(), $info = '') {
        /*
        $log = new mActionLog;
        $log->table = $log->get_table();
        $log->uid   = _uid();
        $log->data  = json_encode(self::diff($old_obj, $new_obj));
        $log->info  = $info;
        $log->uri   = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']: '';
        $log->ip    = ip2long(isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: '');
        $log->oper_type   = $type;
        $log->create_time = time();

        $log->save();
         */
        Queue::push(new jobActionLog(array(
            'uid'   => _uid(),
            'data'  => json_encode(self::diff($old_obj, $new_obj)),
            'info'  => $info,
            'uri'   => isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']: '',
            'ip'    => ip2long(isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: ''),
            'oper_type'   => $type
        )));
    }

    public static function addTowerTaskAction($request_body) {
        $data = json_decode($request_body);
        $action    = 'tower'.$data->action;

        $data      = $data->data;
        $project   = $data->project->name;
        $title     = $data->todo->title;
        $create_by = '';
        if(isset($data->todo->assignee))
            $create_by = $data->todo->assignee->nickname;

        $update_by = '';
        if(isset($data->todo->handler))
            $update_by = $data->todo->handler->nickname;

        return (new mActionLog)->add_task_action($action, $project, $title, $create_by, $update_by);
    }

    public static function addGithubPushAction($request_body) {
        $data = json_decode($request_body);
        $action    = 'gitpush';

        $title     = array();
        $project   = $data->repository->name;
        foreach($data->commits as $commit) {
            $title[] =  $commit->message;
        }
        $title     = implode(',', $title);

        $create_by = '';
        if(isset($data->repository))
            $create_by = $data->repository->owner->name;

        $update_by = '';
        if(isset($data->pusher))
            $update_by = $data->pusher->name;

        return (new mActionLog)->add_task_action($action, $project, $title, $create_by, $update_by);
    }

    public static function fetchGithubPush($cond = null, $refresh = true) {
        $log = new mActionLog('action_task');

        $push_logs  = $log->where('action', 'gitpush');

        if(isset($cond['status'])) {
            $push_logs = $push_logs->where('status', $cond['status']);
        }
        if(isset($cond['create_time'])) {
            $push_logs = $push_logs->where('create_time', '>', $cond['create_time']);
        }
        if(isset($cond['project'])) {
            $push_logs = $push_logs->where('project', $cond['project']);
        }

        $data = $push_logs->get();

        if($refresh)
            $log->where('status', mActionLog::STATUS_NORMAL)
                ->where('action', 'gitpush')
                ->update(array('status' => mActionLog::STATUS_DONE));

        return $data;
    }

    public static function fetchTowerTasks($cond = null, $refresh = true) {
        $log = new mActionLog('action_task');

        $tower_logs = $log->where('action', 'towercompleted');

        if(isset($cond['status'])) {
            $tower_logs = $tower_logs->where('status', $cond['status']);
        }
        if(isset($cond['create_time'])) {
            $tower_logs = $tower_logs->where('create_time', '>', $cond['create_time']);
        }
        if(isset($cond['project'])) {
            $tower_logs = $tower_logs->where('project', 'LIKE', '%'.$cond['project'].'%');
        }

        $data = $tower_logs->get();

        if($refresh)
            $log->where('status', mActionLog::STATUS_NORMAL)
                ->where('action', 'towercompleted')
                ->update(array('status' => mActionLog::STATUS_DONE));

        return $data;
    }

    /**
     * 初始化日志
     */
    public static function init ( $type = 'OTHERS', $prev = array() ) {
        if( is_object( $prev ) ){
            self::$prev = clone $prev;
        }
        else{
            self::$prev = $prev;
        }
        self::$type = self::getActionKey($type);

        return self::$prev;
    }

    /**
     * 保存日志
     */
    public static function save ( $post = array(), $info = '' ) {
        self::$post = $post;

        $data = self::log( self::$type, self::$prev, $post, $info);
        self::$prev = array();
        self::$post = array();

        return $data;
    }

    /**
     * 获取日志表
     */
    public static function get_log($uid, $start_time = 0, $end_time = 99999999999){
        $log   = new mActionLog;
        return $log->get_logs_by_uid($uid, $start_time, $end_time);
    }

    /**
     * 筛选需要记录的字段
     */
    private static function diff( $old_obj, $new_obj ){
        //判断是否相同的instance
        $diff = new \stdClass;

        if( empty($old_obj) && $new_obj ){
            if( is_object($new_obj) ){
                $diff->__class = get_class($new_obj);
                $obj = clone $new_obj;
                $new_obj = new $diff->__class;
                $old_obj = $obj;
            }
            else if( is_array( $new_obj ) ){
                $old_obj = array();
            }
            else{
                return false;
            }
        }
        else if( empty($new_obj) && $old_obj ){
            if( is_object($old_obj)){
                $diff->__class = get_class($old_obj);
                $new_obj = new $diff->__class;
            }
            else if( is_array($old_obj) ){
                $new_obj = array();
            }
            else{
                return false;
            }
        }

        if( is_array( $old_obj ) && is_array($new_obj) ){
            $diff->__class = 'array';
        }
        else if( is_object($old_obj) && is_object($new_obj) ){
            $diff->__class = get_class($old_obj);
        }
        else{
            return false;
        }

        foreach($old_obj as $key=>$val){
            // 如果含有id字段的都可以加入作为记录
            if(!isset($new_obj->$key)){
				$diff->$key = $val;
            }
            else if($new_obj->$key != $val){
				$diff->$key = array($val, $new_obj->$key);
            }
            if(in_array($key, array("id", "_uid", "uid", "__class"))){
				$diff->$key = $val;
            }
		}

		return $diff;
	}

    const TYPE_OTHERS  = 0; //其他
    const TYPE_LOGIN   = 0x1; //账户登录
    const TYPE_LOGOUT  = 0x2; //账户登出
    const TYPE_REGISTER= 0x3; //用户注册

    const TYPE_POST_ASK     = 0x4; //发布求助
    const TYPE_POST_REPLY   = 0x5; //发布作品
    const TYPE_DELETE_ASK   = 0x6; //删除求助
    const TYPE_DELETE_REPLY = 0x7; //删除作品
    const TYPE_VERIFY_ASK   = 0x8; //审核求助
    const TYPE_VERIFY_REPLY = 0x9; //审核作品
    const TYPE_REJECT_ASK   = 0x10; //审核失败求助
    const TYPE_REJECT_REPLY = 0x11; //审核失败作品
    const TYPE_RECOVER_ASK  = 0x12; //恢复求助
    const TYPE_RECOVER_REPLY= 0x13; //恢复作品

    const TYPE_ADD_HELPER   = 0x14; //添加求助账号
    const TYPE_ADD_WORKER   = 0x15; //添加大神账号
    const TYPE_ADD_PARTTIME = 0x16; //添加兼职账号
    const TYPE_ADD_STAFF    = 0x17; //添加后台账号
    const TYPE_ADD_JUNIOR   = 0x18; //添加初级账号

    const TYPE_MODIFY_REMARK   = 0x19; //修改备注
    const TYPE_CHANGE_PASSWORD = 0x20; //修改密码
    const TYPE_MODIFY_USER_INFO = 0x21; //修改用户信息

    const TYPE_PARTTIME_PAID= 0x22; //兼职结算
    const TYPE_STAFF_PAID   = 0x23; //后台结算
    const TYPE_JUNIOR_PAID  = 0x24; //初级账号结算

    const TYPE_ADD_ROLE     = 0x25; //添加角色项
    const TYPE_EDIT_ROLE    = 0x26; //编辑角色项
    const TYPE_ASSIGN_ROLE  = 0x27; //赋予角色
    const TYPE_REVOKE_ROLE  = 0x28; //撤销角色
    const TYPE_UPDATE_PERMISSION    = 0x29; //更新角色权限项
    const TYPE_EDIT_PERMISSION      = 0x30; //更新权限项
    const TYPE_ADD_PERMISSION       = 0x31; //增加权限项
    const TYPE_DELETE_PERMISSION    = 0x32; //删除权限项
    const TYPE_GRANT_PRIVILEGE      = 0x33;  //赋予权限
    const TYPE_REVOKE_PRIVILEGE     = 0x34; //撤销权限

    const TYPE_ADD_RECOMMEND    = 0x35; //添加推荐大神
    const TYPE_SET_RECOMMEND    = 0x36; //设置推荐大神时间
    const TYPE_CANCEL_RECOMMEND = 0x37; //取消推荐大神

    const TYPE_INFORM_PROCESSING    = 0x38; //投诉处理

    const TYPE_VERIFY_COMMENT   = 0x39; //审核评论
    const TYPE_POST_COMMENT     = 0x40; //添加评论
    const TYPE_EDIT_COMMENT     = 0x41; //编辑评论
    const TYPE_DELETE_COMMENT   = 0x42; //删除评论

    const TYPE_POST_SYSTEM_MESSAGE    = 0x43; //发布系统消息
    const TYPE_DELETE_SYSTEM_MESSAGE  = 0x44; //发布系统消息

    const TYPE_ADD_APP    = 0x45;    //新增推荐App
    const TYPE_DELETE_APP = 0x46;    //删除推荐App

    const TYPE_ADD_FEEDBACK    = 0x47;  //新增反馈
    const TYPE_DELETE_FEEDBACK = 0x48;  //删除反馈
    const TYPE_NOTE_FEEDBACK   = 0x49;  //给反馈添加纪录
    const TYPE_MODIFY_FEEDBACK_STATUS = 0x50;    //修改状态

    const TYPE_SET_STAFF_TIME = 0x51;   //设置后台账号登陆时间
    const TYPE_FORBID_USER    = 0x52;   //设置用户禁言

    const TYPE_UPLOAD_FILE  = 0x53;  //上传文件

    const TYPE_REPORT_ABUSE = 0x54;  //新增举报
    const TYPE_DEAL_INFORM  = 0x55;  //处理举报

    const TYPE_SET_SCHEDULE    = 0x56; //设置上班时间
    const TYPE_OFF_DUTY        = 0x57; //设置下班
    const TYPE_DELETE_SCHEDULE = 0x58; //删除上班设置

    const TYPE_PUSH_UMENG   = 0x59;  //推送消息
    const TYPE_REMARK_USER  = 0x60; //备注用户

    const TYPE_DELETE_REVIEW= 0x61; //删除review
    const TYPE_ADD_REMARK   = 0x62; //修改备注

    //MAIN
    const TYPE_UP_ASK     = 0x63; //点赞求助
    const TYPE_FOCUS_ASK  = 0x64; //关注求助
    const TYPE_INFORM_ASK = 0x65; //举报求P
    const TYPE_CANCEL_UP_ASK     = 0x68; //取消点赞求助
    const TYPE_CANCEL_FOCUS_ASK  = 0x69; //取消关注求助
    const TYPE_CANCEL_INFORM_ASK = 0x70; //取消举报求P

    const TYPE_INVITE_FOR_ASK        = 0x66; //邀请求助
    const TYPE_CANCEL_INVITE_FOR_ASK = 0x71; //邀请求助

    const TYPE_ADDED_LABEL = 0x67; //添加标签

    const TYPE_BIND_ACCOUNT   = 0x72;  //绑定
    const TYPE_UNBIND_ACCOUNT = 0x73; //解绑

    const TYPE_UP_COMMENT        = 0x74; //点赞评论
    const TYPE_CANCEL_UP_COMMENT = 0x75; //取消点赞评论
    const TYPE_INFORM_COMMENT        = 0x75; //举报评论
    const TYPE_CANCEL_INFORM_COMMENT = 0x76; //取消举报评论

    const TYPE_DELETE_MESSAGES = 0x77; //删除消息

    const TYPE_UP_REPLY        = 0x78; //点赞作品
    const TYPE_CANCEL_UP_REPLY = 0x79; //取消点赞作品
    const TYPE_COLLECT_REPLY        = 0x80; //收藏作品
    const TYPE_CANCEL_COLLECT_REPLY = 0x81; //取消收藏作品
    const TYPE_INFORM_REPLY = 0x82; //举报作品

    const TYPE_NEW_DEVICE = 0x83; //注册新设备
    const TYPE_USER_CHANGE_DEVICE = 0x84; //用户更换设备登陆

    const TYPE_FOLLOW_USER = 0x85; //关注用户
    const TYPE_UNFOLLOW_USER = 0x86; //取消关注用户
    const TYPE_RESET_PASSWORD = 0x87; //重置密码
    const TYPE_USER_DOWNLOAD = 0x88; //下载
    const TYPE_USER_MODIFY_PUSH_SETTING = 0x89; //修改推送设置

    //PC
    const TYPE_DELETE_DOWNLOAD = 0x90; //删除进行中
    const TYPE_SHARE_ASK = 0x91; //分享求助
    const TYPE_SHARE_REPLY = 0x92; //分享作品
    const TYPE_EDIT_CONFIG = 0x93;

    const TYPE_COUNT_TYPE_NOT_EXIST = 0x94;
    const TYPE_UPDATE_ASK_STATUS = 0x95;
    const TYPE_SET_CONFIG = 0x97;//Admin
    const TYPE_ADD_NEW_COUNT = 0x98;
    const TYPE_ADD_NEW_DEVICE = 0x99;
    const TYPE_UPDATE_COUNT = 0x100;
    const TYPE_DOWNLOAD_FILE = 0x101;
    const TYPE_ADD_NEW_FOCUS = 0x102;
    const TYPE_ADD_NEW_MASTER = 0x103;
    const TYPE_DELETE_MASTER = 0x104;
    const TYPE_NEW_BATCH_REPLY = 0x105;
    const TYPE_UPDATE_REPLY_STATUS = 0x106;
    const TYPE_ADD_NEW_ROLE = 0x107;
    const TYPE_UPDATE_ROLE = 0x108;
    const TYPE_ADD_NEW_UPLOAD = 0x109;
    const TYPE_UPDATE_IMAGE = 0x110;
    const TYPE_SET_MASTER = 0x111;
    const TYPE_SAVE_UMETA = 0x112;
    const TYPE_ADD_NEW_TOKEN= 0x113;
    const TYPE_ADD_NEW_RELATION = 0x114;
    const TYPE_UPDATE_SCORE = 0x115;
    const TYPE_UPDATE_SCORE_CONTENT = 0x116;

    const TYPE_ADD_NEW_CATEGORY = 0x117;
    const TYPE_UPDATE_CATEGORY = 0x118;

    const TYPE_ADD_PUPPET = 0x119;
    const TYPE_EDIT_PUPPET = 0x120;
    const TYPE_UPDATE_PUPPER_RELATION = 0x121;

    const TYPE_ADD_COMMENT_STOCK = 0x122;
    const TYPE_DELETE_COMMENT_STOCK = 0x123;

    const TYPE_MODIFY_REVIEW_STATUS = 0x124;

    const TYPE_ADD_REVIEW = 0x125;
    const TYPE_UPDATE_REVIEW = 0x126;
    //const TYPE_DELETE_REVIEW = 0x127;//exists

    const TYPE_BLOCK_USER_ASKS       = 0x127;
    const TYPE_RESTORE_USER_ASKS     = 0x128;
    const TYPE_BLOCK_USER_REPLIES    = 0x129;
    const TYPE_RESTORE_USER_REPLIES  = 0x130;
    const TYPE_RESTORE_USER_COMMENTS = 0x131;
    const TYPE_BLOCK_USER_COMMENTS   = 0x132;

    const TYPE_RESTORE_COMMENT = 0x133;
    //current type count : 93

    public function data(){
        return array(
            self::TYPE_OTHERS  => '其他',
            self::TYPE_LOGIN   => '账户登录',
            self::TYPE_LOGOUT  => '账户登出',
            self::TYPE_REGISTER=> '用户注册',
            self::TYPE_POST_ASK     => '发布求助',
            self::TYPE_POST_REPLY   => '发布作品',
            self::TYPE_DELETE_ASK   => '删除求助',
            self::TYPE_DELETE_REPLY => '删除作品',
            self::TYPE_VERIFY_ASK   => '审核求助',
            self::TYPE_VERIFY_REPLY => '审核作品',
            self::TYPE_RECOVER_ASK  => '恢复求助',
            self::TYPE_RECOVER_REPLY=> '恢复求助',
            self::TYPE_ADD_HELPER   => '添加求助账号',
            self::TYPE_ADD_WORKER   => '添加大神账号',
            self::TYPE_ADD_PARTTIME => '添加兼职账号',
            self::TYPE_ADD_STAFF    => '添加后台账号',
            self::TYPE_ADD_JUNIOR   => '添加初级账号',
            self::TYPE_ADD_REMARK   => '修改备注',
            self::TYPE_PARTTIME_PAID=> '兼职结算',
            self::TYPE_STAFF_PAID   => '后台结算',
            self::TYPE_JUNIOR_PAID  => '初级账号结算',
            self::TYPE_ADD_ROLE     => '添加角色',
            self::TYPE_EDIT_ROLE    => '编辑角色',
            //self::TYPE_UPDATE_PRIVILEGE => '更新角色权限',
            //self::TYPE_EDIT_PRIVILEGE   => '更新权限',
            //self::TYPE_ADD_PRIVILEGE    => '更新权限',
            self::TYPE_ADD_RECOMMEND    => '添加推荐大神',
            self::TYPE_SET_RECOMMEND    => '设置推荐大神时间',
            self::TYPE_INFORM_PROCESSING=> '投诉处理',
            self::TYPE_VERIFY_COMMENT   => '审核评论',
            self::TYPE_POST_COMMENT     => '添加评论',
            self::TYPE_EDIT_COMMENT     => '编辑评论',
            self::TYPE_DELETE_COMMENT   => '删除评论',
            self::TYPE_POST_SYSTEM_MESSAGE    => '发布系统消息',
            self::TYPE_DELETE_SYSTEM_MESSAGE  => '删除系统消息',
            self::TYPE_ADD_APP                => "新增推荐App",
            self::TYPE_DELETE_APP             => "删除推荐App",
            self::TYPE_ADD_FEEDBACK           => "新增反馈",
            self::TYPE_DELETE_FEEDBACK        => "删除反馈",
            self::TYPE_NOTE_FEEDBACK          => "给反馈添加纪录",
            self::TYPE_MODIFY_FEEDBACK_STATUS => "修改状态",
            self::TYPE_DELETE_PERMISSION      => "删除权限项",
            self::TYPE_ASSIGN_ROLE            => "赋予角色",
            self::TYPE_REVOKE_ROLE            => "撤销角色",
            self::TYPE_GRANT_PRIVILEGE        => "赋予权限",
            self::TYPE_REVOKE_PRIVILEGE       => "撤销权限",
            self::TYPE_SET_STAFF_TIME         => "设置后台账号登陆时间",
            self::TYPE_FORBID_USER            => "设置用户禁言",
            self::TYPE_UPLOAD_FILE            => "上传文件",
            self::TYPE_REPORT_ABUSE           => "新增举报",
            self::TYPE_DEAL_INFORM            => "处理举报",
            self::TYPE_SET_SCHEDULE           => "设置上班时间",
            self::TYPE_OFF_DUTY               => "设置下班",
            self::TYPE_DELETE_SCHEDULE        => "删除上班设置",
            self::TYPE_PUSH_UMENG             => "推送消息",
            self::TYPE_REMARK_USER            => "备注用户",
            self::TYPE_MODIFY_USER_INFO       => "修改用户信息",
            self::TYPE_DELETE_REVIEW          => "删除review",
            self::TYPE_ADD_REMARK              => "修改备注",
            self::TYPE_UP_ASK                  => "点赞求助",
            self::TYPE_FOCUS_ASK               => "关注求助",
            self::TYPE_INFORM_ASK              => "举报求P",
            self::TYPE_CANCEL_UP_ASK           => "取消点赞求助",
            self::TYPE_CANCEL_FOCUS_ASK        => "取消关注求助",
            self::TYPE_CANCEL_INFORM_ASK       => "取消举报求P",
            self::TYPE_INVITE_FOR_ASK          => "邀请求助",
            self::TYPE_CANCEL_INVITE_FOR_ASK   => "邀请求助",
            self::TYPE_ADDED_LABEL             => "添加标签",
            self::TYPE_BIND_ACCOUNT            => "绑定",
            self::TYPE_UNBIND_ACCOUNT          => "解绑",
            self::TYPE_UP_COMMENT              => "点赞评论",
            self::TYPE_CANCEL_UP_COMMENT       => "取消点赞评论",
            self::TYPE_INFORM_COMMENT          => "举报评论",
            self::TYPE_CANCEL_INFORM_COMMENT   => "取消举报评论",
            self::TYPE_POST_INFORM             => "添加举报（带举报内容",
            self::TYPE_DELETE_MESSAGES         => "删除消息",
            self::TYPE_UP_REPLY                => "点赞作品",
            self::TYPE_CANCEL_UP_REPLY         => "取消点赞作品",
            self::TYPE_COLLECT_REPLY           => "收藏作品",
            self::TYPE_CANCEL_COLLECT_REPLY    => "取消收藏作品",
            self::TYPE_INFORM_REPLY            => "举报作品",
            self::TYPE_NEW_DEVICE              => "注册新设备",
            self::TYPE_USER_CHANGE_DEVICE      => "用户更换设备登陆",
            self::TYPE_FOLLOW_USER             => "关注用户",
            self::TYPE_UNFOLLOW_USER           => "取消关注用户",
            self::TYPE_RESET_PASSWORD          => "重置密码",
            self::TYPE_USER_DOWNLOAD           => "下载",
            self::TYPE_USER_MODIFY_PUSH_SETTING=> "修改推送设置",
            self::TYPE_DELETE_DOWNLOAD => "删除进行中",
            self::TYPE_SHARE_ASK       => "分享求助",
            self::TYPE_SHARE_REPLY     => "分享作品",
            self::TYPE_COUNT_TYPE_NOT_EXIST => "Count类型不存在",
            self::TYPE_UPDATE_ASK_STATUS => "更新求助",
            self::TYPE_SET_CONFIG => "设置配置？",//Admin
            self::TYPE_ADD_NEW_COUNT => "新增Count？",
            self::TYPE_ADD_NEW_DEVICE => "注册新设备",
            self::TYPE_UPDATE_COUNT => "更新Count",
            self::TYPE_DOWNLOAD_FILE => "下载文件",
            self::TYPE_ADD_NEW_FOCUS => "关注",
            self::TYPE_ADD_NEW_MASTER => "新增大神",
            self::TYPE_DELETE_MASTER => "删除大神",
            self::TYPE_NEW_BATCH_REPLY => "新增批量作品",
            self::TYPE_UPDATE_REPLY_STATUS => "更新作品状态",
            self::TYPE_ADD_NEW_ROLE => "新增角色",
            self::TYPE_UPDATE_ROLE => "更新角色（名称？类型？）",
            self::TYPE_ADD_NEW_UPLOAD => "新上传文件",
            self::TYPE_UPDATE_IMAGE => "更新上传图片信息",
            self::TYPE_SET_MASTER => "设置大神推荐时间",
            self::TYPE_SAVE_UMETA => "保存Usermeta",
            self::TYPE_ADD_NEW_TOKEN => "新增Token",
            self::TYPE_ADD_NEW_RELATION => "新增用户关系",
            self::TYPE_UPDATE_SCORE => "更新积分",
            self::TYPE_UPDATE_SCORE_CONTENT => "更新积分理由",
        );
    }

    public static function getActionKey($name, $negative= '') {
        if($negative != '') $negative = $negative.'_';
        $action_key = 'self::TYPE_'.$negative;
        $action_key .= $name;

        if(defined($action_key)) {
            return constant($action_key);
        }
        else {
            return false;
        }
    }
}
