<?php namespace App\Trades;

use Illuminate\Database\Eloquent\Model,
    App\Traits\SoftDeletes;

class TradeBase extends Model {
    public $timestamps = true;
    const TYPE_ACCOUNT_EARN = 1;
    const TYPE_ACCOUNT_PAY = 2;
    const TYPE_ACCOUNT_FREEZE = 3;
    const TYPE_ACCOUNT_UNFREEZEE = 4;

    public function __construct($uid) {
        parent::__construct();

        $this->uid = $uid;
        return $this;
    }

    /**
     * 保存
     */
    public function save(array $options = []) {
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

        //调用laravel的model魔术方法
        if( $func != 'get' && $func != 'set' ) {
            return parent::__call($name, $arguments);
        }

        if( !in_array($key, $this->keys) ) {
            return error('WRONG_ARGUMENTS', '没有相应的键值');
        }
        if( $func == 'get' ) {
            return $this->$key;
        }

        if( is_array($arguments) && isset($arguments[0]) ) {
            $this->$key = $arguments[0];
        }
        return $this;
    }
}
