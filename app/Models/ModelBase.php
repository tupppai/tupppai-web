<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model,
    App\Traits\SoftDeletes;


class ModelBase extends Model
{
    //Reply
    const TYPE_NORMAL       = 1;
    //Message:type
    //Device
    const TYPE_UNKNOWN = -1;
    const TYPE_ANDROID = 0;
    const TYPE_IOS     = 1;
    //Role                      //UserRole
    const TYPE_HELP     =  1;    const ROLE_HELP     =  1;
    const TYPE_WORK     =  2;    const ROLE_WORK     =  2;
    const TYPE_PARTTIME =  3;    const ROLE_PARTTIME =  3;
    const TYPE_STAFF    =  4;    const ROLE_STAFF    =  4;
    const TYPE_NEWBIE   =  5;    const ROLE_NEWBIE   =  5;
    const TYPE_GENERAL  =  6;    const ROLE_GENERAL  =  6;
    const TYPE_STAR     =  7;    const ROLE_STAR     =  7;
    const TYPE_BLOCKED  =  8;    const ROLE_BLOCKED  =  8;
    const TYPE_BLACKLIST=  9;    const ROLE_BLACKLIST=  9;
    const TYPE_CRITIC   = 10;    const ROLE_CRITIC   = 10;
    const TYPE_TRUSTABLE= 11;    const ROLE_TRUSTABLE= 11;

    //UserLanding
    const TYPE_WEIXIN = 1;
    const TYPE_WEIBO  = 2;
    const TYPE_QQ     = 3;
    //UserSettlement
    const TYPE_PAID = 2;
    //Vote:status
    //Sysmsg:消息类型
    const MSG_TYPE_NOTICE   = 1; //普通
    const MSG_TYPE_ACTIVITY = 2; //活动
    //Inform,Sysmsg,Message
    const TYPE_ASK      = 1;
    const TYPE_REPLY    = 2;
    const TYPE_COMMENT  = 3;
    const TYPE_USER     = 4;
    const TYPE_SYSTEM   = 5;
    const TYPE_URL      = 6;
    const TYPE_CATEGORY = 7;
    //SysMsg
    //Feeedback:status
    //Master:status
    //Review:status
    //UserScheduling:status
    //UserScore:status
    const TARGET_TYPE_URL = 0; //跳转URL

    //const STATUS_REPLIED = 4;//如果回复过求P 这里置为已完成//Download
    const STATUS_DONE    = 2;//状态已完成
    const STATUS_NORMAL  = 1;//状态正常
    const STATUS_DELETED = 0;//状态已删除
    const STATUS_READY   = -1;//预发布(审核中)//Ask
    const STATUS_BANNED  = -2;//屏蔽
    const STATUS_REJECT  = -3;//拒绝状态
    const STATUS_CHECKED = -4;//categories,再审核
    const STATUS_HIDDEN  = -5;//不需要显示的
    const STATUS_BLOCKED = -6;//屏蔽用户时刷状态
    const STATUS_FROZEN  = -7;//屏蔽用户时刷状态
    const STATUS_FAILED  = -8;//失败;

    //Inform
    const INFORM_STATUS_IGNORED  = 0; //删除
    const INFORM_STATUS_PENDING  = 1; //已举报，待处理
    const INFORM_STATUS_SOLVED   = 2; //已处理
    const INFORM_STATUS_REPLACED = 3; //重复举报

    //系统协助控制的时间
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    const DELETED_AT = 'delete_time';

    //Count
    const ACTION_UP             =  1;
    const ACTION_LIKE           =  2;
    const ACTION_COLLECT        =  3;
    const ACTION_DOWN           =  4;
    const ACTION_SHARE          =  5;
    const ACTION_WEIXIN_SHARE   =  6;
    const ACTION_INFORM         =  7;
    const ACTION_CLICK          =  8;
    const ACTION_COMMENT        =  9;
    const ACTION_REPLY          = 10;
    const ACTION_TIMELINE_SHARE = 11;
    //super like
    const COUNT_LOVE            = 4;

    //platform
    const PF_WEIXIN = 'weixin';
    const PF_QZONE  = 'qzone';

    //ThreadCategory
    const CATEGORY_TYPE_NORMAL  = 0;
    const CATEGORY_TYPE_POPULAR = 1;
    const CATEGORY_TYPE_PC_POPULAR  = 2;
    const CATEGORY_TYPE_APP_POPULAR = 3;
    const CATEGORY_TYPE_ACTIVITY    = 4;
    const CATEGORY_TYPE_CHANNEL     = 5;
    const CATEGORY_TYPE_TUTORIAL    = 6;
    const CATEGORY_TYPE_TIMELINE    = 7;
    const CATEGORY_TYPE_WX_ACTIVITY = 8;

    //User
    const SEX_MAN   = 1;
    const SEX_FEMALE= 0;

    //UserDevice
    const VALUE_OFF  = '0';
    const VALUE_ON   = '1';

    //UserDevice
    const PUSH_TYPE_COMMENT = 'comment';
    const PUSH_TYPE_FOLLOW  = 'follow';
    const PUSH_TYPE_INVITE  = 'invite';
    const PUSH_TYPE_REPLY   = 'reply';
    const PUSH_TYPE_SYSTEM  = 'system';
    const PUSH_TYPE_LIKE    = 'like';

    //Usermeta
    const KEY_REMARK = 'remark';
    const KEY_FORBID = 'forbid_speech'; //禁言
    const KEY_LAST_READ_COMMENT = 'last_read_comment';
    const KEY_LAST_READ_FOLLOW  = 'last_read_fellow';
    const KEY_LAST_READ_INVITE  = 'last_read_invite';
    const KEY_LAST_READ_REPLY   = 'last_read_reply';
    const KEY_LAST_READ_NOTICE  = 'last_read_notice';
    const KEY_LAST_READ_LIKE    = 'last_read_like';
    //Usermeta 后台用
    const KEY_LAST_READ_FEEDBACK_TIME = 'last_read_feedback_time';
    //config
    const KEY_STAFF_TIME_PRICE_RATE = 'user.staff_time_price_rate';
    const KEY_WITHDRAW_MIN_AMOUNT = 'account.min_withdraw_amount';
    const KEY_WITHDRAW_MAX_AMOUNT = 'account.max_withdraw_amount';

    //category type
    const CATEGORY_TYPE_REPLIES = 2;
    const CATEGORY_TYPE_ASKS = 1;

    //UserRole(shouldn't be const)
    const SUPER_USER_UID = 1;

    public function __construct()
    {
        parent::__construct();
        // 创建的时候初始化
        $this->beforeCreate();
    }

    /**
     * 保存之前
     */
    public function beforeSave() {
        return $this;
    }

    /**
     * 保存之后
     */
    public function beforeCreate() {
        return $this;
    }

    /**
     * 赋值
     */
    public function assign( $data ) {
        // return $this->assign($data);
        foreach($data as $key=>$val) {
            $this->{$key} = $val;
        }

        return $this;
    }

    /**
     * 保存
     */
    public function save(array $options = []) {
        $this->beforeSave();

        $result = parent::save($options);

        if($result == false){
            $str = "Save data error: " . implode(',', $this->getMessages());
            return error('SYSTEM_ERROR', $str);
        }

        return $this;
    }

    /**
     * 软删除
     */
    public function delete() {
        if( is_null($this->delete_time) ) {
            $this->delete_time = "".time();
        }
        $this->status = self::STATUS_DELETED;

        //todo: delete log
        $this->save();
    }

    /**
     * 恢复
     */
    public function restore() {
        if( isset($this->delete_time) ) {
            $this->delete_time = null;
        }
        $this->status = self::STATUS_NORMAL;

        //todo: restore log
        $this->save();
    }

    /**
     * 验证是否存在，否则给默认值
     */
    public function check($model, $column, $default = 0){
        if(isset($model->$column) and isset($this->$column)){
            $this->$column = $model->$column;
        }
        else {
            $this->$column = $default;
        }
        return $this->$column;
    }


    /**
     * 默认使用时间戳戳功能
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 获取当前时间
     *
     * @return int
     */
    public function freshTimestamp() {
        return time();
    }

    /**
     * 避免转换时间戳为时间字符串
     *
     * @param DateTime|int $value
     * @return DateTime|int
     */
    public function fromDateTime($value) {
        return $value;
    }

    /**
     * select的时候避免转换时间为Carbon
     *
     * @param mixed $value
     * @return mixed
     */
//  protected function asDateTime($value) {
//      return $value;
//  }

    /**
     * 从数据库获取的为获取时间戳格式
     *
     * @return string
     */
    public function getDateFormat() {
        return 'U';
    }

    public function getDates() {
        return [];
    }


    /**
     * QueryBuilder分页
     * @param  [object]    $builder [QueryBuilder]
     * @param  [integer]   $page    [页码]
     * @param  [integer]   $limit   [单页大小]
     * @return [simple]    $pi      [分页器]
     */
    public static function query_page($builder, $page=1, $limit=0, $options = array())
    {
        if( $limit != 0 ) {
            //$page = ($page - 1 < 0)?0: $page - 1;
            //$builder = $builder->skip($page*$limit)->take($limit);
            $builder = $builder->forPage( $page, $limit );
        }

        return $builder->get();
    }

    /**
     * 统计query的总数量
     */
    public static function query_sum($builder, $options = array()) {
        return $builder->count();
    }

    /**
     * [query_builder 获取一个QueryBuilder]
     * @return [object] $builder   [Builder]
     */
    public static function query_builder($alias = '')
    {
        $class = get_called_class();

        $builder = new $class;
        $table_name = $builder->getTable();
        $builder = $builder->valid()
            ->lastUpdated()
            ->orderBy($table_name.'.create_time', 'DESC');

        return $builder;
    }

    /**
     * 格式化返回的数据
     */
    public static function format($value, $type = 'number'){
        switch ($type){
        case 'number':
            return isset($value)?$value: 0;
        }
        return '';
    }

    private function getScopeTable($table = null) {
        if($table) {
            return $table;
        }
        else {
            return $this->getTable();
        }

    }
    public function scopeValid( $query , $table = null){
        $table = $this->getScopeTable($table);
        return $query->where( $table.'.status', '>', self::STATUS_DELETED );
    }
    public function scopeNormal( $query ){
        $table = $this->getTable();
        return $query->where( $table.'.status', self::STATUS_NORMAL );
    }
    public function scopeInvalid( $query ){
        $table = $this->getTable();
        return $query->where( $table.'.status', self::STATUS_DELETED );
    }
    public function scopeLastUpdated($query) {
	$last_updated = intval(_req('last_updated'));
        if( $last_updated > time()) {
            $table = $this->getTable();
            return $query->where($table.'.create_time', '<', $last_updated);
        }
        return $query;
    }
    public function scopeBlocking($query, $uid, $table = null) {
        if( is_null($table) ){
            $table = $this->getScopeTable($table);
        }
        //加上自己的广告贴
        $query = $query->where(function($query) use ($table, $uid) {
            $query = $query->where( $table.'.status', ">", self::STATUS_DELETED );
            if( $uid = _uid()){
                $query = $query->orWhere([ "$table.uid" => $uid, "$table.status" => self::STATUS_BLOCKED ]);
            }
        });
        return $query;
    }
    public function scopeBlockingUser($query, $uid, $table = null) {
        $table = $this->getScopeTable($table);
        //加上自己的广告贴
        $query = $query->whereNotIn("$table.uid", function($query) use ($uid) {
            $query = $query->from('follows')
                ->select('follow_who')
                ->where( 'follows.status', '=', self::STATUS_BLOCKED )
                ->where('follows.uid', '=', $uid);
        });
        return $query;
    }
    public static function _blocking($table_name) {

        return function($query) use ($table_name) {
            //加上自己的广告贴
            $query = $query->where("$table_name.status", '>', self::STATUS_DELETED );
            if( $uid = _uid()){
                $query = $query->orWhere([ "$table_name.uid" => $uid, "$table_name.status" => self::STATUS_BLOCKED ]);
            }
        };
    }

}
