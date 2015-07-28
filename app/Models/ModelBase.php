<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelBase extends Model
{
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

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'status',
                'value' => self::STATUS_DELETED
            )
        ));
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
     * phalcon 坑
     */
    public function beforeValidationOnCreate() {
        $metaData = $this->getModelsMetaData();
        $attributes = $metaData->getNotNullAttributes($this);

        // Set all not null fields to their default value.
        foreach($attributes as $field) {
            if(!isset($this->{$field}) || is_null($this->{$field})) {
                $this->{$field} = new RawValue('default');
            }
        }
    }

    /**
     * 保存
     */
    public function save(array $options = []) {
        return $result = parent::save($options);
/*
        if($result == false){
            $str = "Save data error: " . implode(',', $this->getMessages());
            if (false) {
                $this->getDI()->getDebug_log()->error($str);
            }
            return error(1, $str);
        }
        return $this;
 */
    }

    /**
     * 保存对象并且返回这个对象（可访问自增主键）
     *
     * @param  \Phalcon\Mvc\Model  $obj    对象
     * @param  boolean $exception_on_error 保存失败时是否抛出异常
     * @return $obj | false
     */
    public function save_and_return($obj, $exception_on_error=true)
    {
        if ($obj->save() == false) {
            $str = "Save data error: " . implode(',', $obj->getMessages());
            if ($exception_on_error) {
                echo $str;
                pr($obj);
                //throw new Exception($str, 1);
            } else {
                $this->getDI()->getDebug_log()->error($str);
            }
            return false;
        } else {
            return $obj;
        }
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
        //todo cache get_called_class
        if( $last_updated = _req('last_updated') )
            $builder->andWhere("create_time > $last_updated");

        $page = ($page - 1 < 0)?0: $page - 1;
        if( $limit != 0 )
            $builder->limit($limit, $page*$limit);

        return $builder->getQuery()->execute();
    }

    /**
     * [query_builder 获取一个QueryBuilder]
     * @return [object] $builder   [Builder]
     */
    public static function query_builder($alias = '')
    {
        $modelsManager = get_di('modelsManager');
        $builder = $modelsManager
            ->createBuilder()
            ->from(get_called_class());

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
