<?php namespace App\Trades\Models;

use Illuminate\Database\Eloquent\Model,
    App\Traits\SoftDeletes;

class ModelBase extends Model {
    public $timestamps = true;

    public function beforeSave() {

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
     * 魔术方法 Getter/Setter
     */
    public function __call($name, $arguments)
    {
        $func   = substr($name, 0, 3);
        $key    = camel_to_lower(substr($name, 3));
        if( !in_array($key, $this->keys) ) {
            return error('WRONG_ARGUMENTS', '没有相应的键值');
        }
        if( $func == 'get' ){
            return $this->$key;
        }
        if( is_array($arguments) && isset($arguments[0]) ) {
            $this->$key = $arguments[0];
        }
        return $this;
    }
}
