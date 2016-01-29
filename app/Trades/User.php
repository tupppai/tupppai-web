<?php namespace App\Trades;

use App\Models\User as mUser;
use App\Trades\Account as tAccount;

class User extends TradeBase
{
    public $table = 'users';

    /**
     * 设置冻结金额
     */
    public static function setFreezing($uid, $freezing)
    {
        $user = mUser::where('uid', $uid)->first();
        $user->freezing = $freezing;
        $user->save();

        return $user;
    }

    /*
     * 获取用户冻结金额
     * */
    public static function getFreezing($uid)
    {
        $user = mUser::where('uid', $uid)->first();
        return $user->freezing;
    }

    /**
     * 设置账户余额
     */
    public static function setBalance($uid, $balance)
    {
        $user = mUser::where('uid', $uid)->first();

        if($balance < 0) {
            tAccount::writeLog($uid, $amount, $balance, self::STATUS_FAILED, self::TYPE_OUTCOME, '余额不足');
        }
        $user->balance = $balance;
        $user->save();
    }

    /**
     * 获取用户余额
     */
    public static function getBalance($uid)
    {
        $user = mUser::where('uid', $uid)->first();
        return $user->balance;
    }

    /*
     * 增加用户余额
     */
    public static function addBalance($uid, $amount, $info = '入账', $extra = '', $status = self::STATUS_NORMAL)
    {
        $balance = self::getBalance($uid) + $amount;
        self::setBalance($uid, $balance ,$amount);

        tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_INCOME, $info, $extra);
        return $balance;
    }

    /*
     * 解除冻结
     */
    public static function reduceBalance($uid, $amount, $info = '扣款', $extra = '', $status = self::STATUS_NORMAL)
    {
        $balance = self::getBalance($uid) - $amount;
        self::setBalance($uid, $balance ,$amount);

        tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_OUTCOME, $info, $extra);
        return $balance;
    }

    /*
     * 增加冻结金额
     */
    public static function addFreezing($uid, $amount, $info = '冻结', $extra = '', $status = self::STATUS_NORMAL)
    {
        $freezing = self::getFreezing($uid);
        $freezing = ($freezing + $amount);
        self::setFreezing($uid, $freezing);

        tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_FREEZE, $info, $extra);
        return $freezing;
    }

    /*
     * 扣除冻结金额
     */
    public static function reduceFreezing($uid, $amount, $info = '解除冻结', $extra = '', $status = self::STATUS_NORMAL)
    {
        $freezing = self::getFreezing($uid);
        $freezing = ($freezing - $amount);
        self::setFreezing($uid, $freezing);

        tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_UNFREEZE, $info, $extra);
        return $freezing;
    }

    /**
     * 后台付款
     */
    public static function pay($uid, $sellerUid, $amount)
    {
        self::addBalance($sellerUid, $amount);
        self::reduceBalance($uid, $amount);
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
}
