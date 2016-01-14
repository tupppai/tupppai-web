<?php namespace App\Trades;

use App\Services\User as sUser;

class User extends TradeBase {
    public $table = 'users';

    /**
     * 设置账户余额
     */
    public static function setBalance($uid, $balance) {
        $user = sUser::getUserByUid($uid);

        $user->balance = $balance;
        $user->save();
    }

    /**
     * 获取用户余额
     */
    public static function getBalance($uid) {
        return sUser::getUserBalance($uid);
    }
 
    /*
     * 检查扣除商品费用后,用户余额是否充足
     * */
    public function checkBalance($uid, $amount)
    {
        $balance = self::getBalance($uid);
        $balance = ($balance - $amount);
        if(0 > $balance){
            return false;
        }
        return true;
    }

    /**
     * 设置冻结金额
     */
    public static function setFreezing($uid, $amount) {
        $user = sUser::getUserByUid($uid);

        $user->freezing = $amount;
        $user->save();

        return $user;
    }

    /*
     * 获取用户冻结金额
     * */
    public static function getFreezing($uid)
    {
        return sUser::getUserFreezing($uid);
    }
    
    /*
     * 冻结金额
     */
    public function freezeBalance($uid, $amount)
    {
        $userGoodsBalance   = self::getUserGoodsBalance($uid, $amount);
        self::setBalance($uid, $userGoodsBalance);
        //设置冻结
        $userGoodsFreezing  = self::getUserGoodsFreezing($uid, $amount);
        self::setFreezing($uid, $userGoodsFreezing);
    }

    /**
     * 获取账户流水记录
     */
    public static function getUserAccounts($uid) {
    }

    /**
     * 获取用户订单流水
     */
    public static function getUserOrders($uid) {
    }

}
