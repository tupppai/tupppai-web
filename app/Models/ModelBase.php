<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model,
    App\Traits\SoftDeletes;


class ModelBase extends Model
{
    /**
     * 求助
     */
    const TYPE_ASK = 1;
    /**
     * 回复的作品
     */
    const TYPE_REPLY = 2;
    /**
     * 评论
     */
    const TYPE_COMMENT = 3;

    /**
     * 如果回复过求P 这里置为已完成
     */
    const STATUS_REPLIED = 4;
    /**
     * 预发布(审核中)
     */
    const STATUS_READY = 3;
    /**
     * 拒绝状态
     */
    const STATUS_REJECT = 2;
    /**
     * 状态正常
     */
    const STATUS_NORMAL = 1;
    /**
     * 状态已删除
     */
    const STATUS_DELETED= 0;
    /**
     * 屏蔽
     */
    const STATUS_BANNED = -1;

    //系统协助控制的时间
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    const DELETED_AT = 'delete_time';

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
        // return $this->fill($data);
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
            if (false) {
                //$this->getDI()->getDebug_log()->error($str);
            }
            return error(1, $str);
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
            $page = ($page - 1 < 0)?0: $page - 1;
            $builder = $builder->skip($page*$limit)->take($limit);
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
        $builder = $builder->where ('status', '=', self::STATUS_NORMAL);
        if( $last_updated = _req('last_updated') ) {
            $builder = $builder->where('create_time', '<', $last_updated);
        }

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
}
