<?php namespace App\Trades;

use App\Models\User as mUser;
use App\Trades\Account as tAccount;

class User extends TradeBase
{
    public $table = 'users';
    const SYSTEM_USER_ID = 1;

    /**
     * 获取用户余额
     */
    public static function getBalance($uid)
    {
        $user = mUser::where('uid', $uid)->first();
        if(!$user) {
            return error('USER_NOT_EXIST');
        }
        return $user->balance;
    }

    /*
     * 获取用户冻结金额
     * */
    public static function getFreezing($uid)
    {
        $user = mUser::where('uid', $uid)->first();
        if(!$user) {
            return error('USER_NOT_EXIST');
        }
        return $user->freezing;
    }

    /**
     * 设置账户余额
     */
    public static function setBalance($uid, $balance ,$amount)
    {
        $user = mUser::where('uid', $uid)->first();
        if(!$user) {
            return error('USER_NOT_EXIST');
        }

        if($balance < 0) {
            tAccount::writeLog($uid, $amount, $balance, tAccount::STATUS_FAILED, tAccount::TYPE_OUTCOME, '余额不足');
        }
        $user->balance = $balance;
        return $user->save();
    }

    /**
     * 设置冻结金额
     */
    public static function setFreezing($uid, $freezing)
    {
        $user = mUser::where('uid', $uid)->first();
        if(!$user) {
            return error('USER_NOT_EXIST');
        }
        
        $user->freezing = $freezing;
        return $user->save();
    }
    
    /*
     * 增加用户余额
     */
    public static function addBalance($uid, $amount, $info = '入账')
    {
        $balance = self::getBalance($uid) + $amount;
        self::setBalance($uid, $balance ,$amount);

        tAccount::writeLog($uid, $amount, $balance, tAccount::STATUS_NORMAL, tAccount::TYPE_INCOME, $info);
        return $balance;
    }

    /*
     * 扣除用户金额
     */
    public static function reduceBalance($uid, $amount, $info = '扣款')
    {
        $balance = self::getBalance($uid) - $amount;
        self::setBalance($uid, $balance ,$amount);

        tAccount::writeLog($uid, $amount, $balance, tAccount::STATUS_NORMAL, tAccount::TYPE_OUTCOME, $info);
        return $balance;
    }

    /*
     * 增加冻结金额
     */
    public static function addFreezing($uid, $amount, $info = '冻结')
    {
        $balance  = self::getBalance($uid);
        $freezing = self::getFreezing($uid) + $amount;
        self::setFreezing($uid, $freezing);

        tAccount::writeLog($uid, $amount, $balance, tAccount::STATUS_NORMAL, tAccount::TYPE_FREEZE, $info);
        return $freezing;
    }

    /*
     * 扣除冻结金额
     */
    public static function reduceFreezing($uid, $amount, $info = '解除冻结')
    {
        $balance  = self::getBalance($uid);
        $freezing = self::getFreezing($uid) - $amount;
        self::setFreezing($uid, $freezing);

        tAccount::writeLog($uid, $amount, $balance, tAccount::STATUS_NORMAL, tAccount::TYPE_UNFREEZE, $info);
        return $freezing;
    }

    /**
     * 后台接口，转账
     */
    public static function pay($uid, $seller_uid, $amount)
    {
        //扣除购买人金额
        self::reduceBalance($uid, $amount);
        //增加卖家余额
        self::addBalance($seller_uid, $amount);
    }

    /**
     * 获取账户流水记录
     */
    public static function getUserAccounts($uid, $page, $size)
    {
        return tAccount::where('uid', $uid)
            ->forPage($page, $size)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 获取用户订单流水
     */
    public static function getUserOrders($uid, $page, $size)
    {
        return tOrders::where('uid', $uid)
            ->forPage($page, $size)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 检查支付余额是否充足
     */
    public static function checkUserBalance($uid,$amount)
    {
        $balance = self::getBalance($uid);
        $balance = ($balance - $amount);
        if (0 > $balance) {
            return false;
        }
        return true;

    }

    public function __construct()
    {

    }
}
