<?php namespace App\Trades;

use Illuminate\Database\Eloquent\Model,
    App\Traits\SoftDeletes;

class TradeBase extends Model {
    public $timestamps = true;

    //交易状态 
    const STATUS_DELETED    = 0;// 删除
    const STATUS_NORMAL     = 1;// 成功
    const STATUS_PAYING     = 2;// 支付中
    const STATUS_TIMEOUT    = 3;// 超时
    const STATUS_UNCERTAIN  = 4;// 不确定
    const STATUS_FAILED     = -1;// 失败

    // 账户类型
    const TYPE_INCOME   = 1; //入账
    const TYPE_OUTCOME  = 2; //出账
    const TYPE_FREEZE   = 3; //冻结
    const TYPE_UNFREEZE = 4; //解冻

    // 支付类型
    const PAYMENT_TYPE_CASH     = 1; //站内余额
    const PAYMENT_TYPE_WECHAT   = 2; //微信
    const PAYMENT_TYPE_WECHAT_RED       = 3; //微信红包
    const PAYMENT_TYPE_WECHAT_TRANSFER  = 4; //微信企业转账
    const PAYMENT_TYPE_ALIPAY   = 5; //阿里支付
    const PAYMENT_TYPE_UNION    = 6; //银联
    const PAYMENT_TYPE_CREDIT   = 7; //信用卡

    // 订单类型
    const ORDER_TYPE_INSIDE     = 1;    //站内订单
    const ORDER_TYPE_OUTSIDE    = 2;   //站外订单订单

    const SYSTEM_USER_ID = 1;

    public function __construct() {
        parent::__construct();
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
            return error('WRONG_ARGUMENTS', '没有相应的键值' . $key);
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
