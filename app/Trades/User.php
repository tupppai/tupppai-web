<?php namespace App\Trades;

use App\Models\User as mUser;
use App\Trades\Account as tAccount;
use DB, Log;

class User extends TradeBase
{
    public $table = 'users';

    const USERKEY = 'uid';

    /**
     * 获取用户余额
     */
    public static function getBalance($uid)
    {
        $user = mUser::where(self::USERKEY, $uid)->first();
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
        $user = mUser::where(self::USERKEY, $uid)->first();
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
        $user = mUser::where(self::USERKEY, $uid)->first();
        if(!$user) {
            return error('USER_NOT_EXIST');
        }

        if($balance < 0) {
            tAccount::writeLog($uid, $amount, $balance, self::STATUS_FAILED, self::TYPE_OUTCOME, '余额不足');
        }
        $user->balance = $balance;
        return $user->save();
    }

    /**
     * 设置冻结金额
     */
    public static function setFreezing($uid, $freezing)
    {
        $user = mUser::where(self::USERKEY, $uid)->first();
        
        if(!$user) {
            return error('USER_NOT_EXIST');
        }
        
        $user->freezing = $freezing;
        return $user->save();
    }

    /*
     * 增加用户余额
     */
    public static function addBalance($uid, $amount, $info = '入账', $extra = '', $status = self::STATUS_NORMAL)
    {
        $balance = self::getBalance($uid) + $amount;
        $balance = self::setBalance($uid, $balance ,$amount);
        $balance = $balance->balance;
        return tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_INCOME, $info, $extra);
    }

    /*
     * 解除冻结
     */
    public static function reduceBalance($uid, $amount, $info = '扣款', $extra = '', $status = self::STATUS_NORMAL)
    {
        $balance = self::getBalance($uid) - $amount;
        self::setBalance($uid, $balance ,$amount);

        return tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_OUTCOME, $info, $extra);
    }

    /*
     * 增加冻结金额
     */
    public static function addFreezing($uid, $amount, $info = '冻结', $extra = '', $status = self::STATUS_NORMAL)
    {
        $balance  = self::getBalance($uid);
        $freezing = self::getFreezing($uid) + $amount;
        self::setFreezing($uid, $freezing);

        return tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_FREEZE, $info, $extra);
    }

    /*
     * 扣除冻结金额
     */
    public static function reduceFreezing($uid, $amount, $info = '解除冻结', $extra = '', $status = self::STATUS_NORMAL)
    {
        $balance  = self::getBalance($uid);
        $freezing = self::getFreezing($uid) - $amount;
        self::setFreezing($uid, $freezing);

        return tAccount::writeLog($uid, $amount, $balance, $status, self::TYPE_UNFREEZE, $info, $extra);
    }

    /**
     * 后台付款
     */
    public static function pay($uid, $sellerUid, $amount, $info = '')
    {
        try {
            DB::beginTransaction();
            //扣除购买人金额
            self::addBalance($sellerUid, $amount, '入账-'.$info);
            //增加卖家余额
            self::reduceBalance($uid, $amount, '扣款-'.$info);
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            Log::info('transfer err', array($e));
        }
    }

    /**
     * 获取账户流水记录
     */
    public static function getUserAccounts($uid, $page, $size)
    {
        return tAccount::where(self::USERKEY, $uid)
            ->forPage($page, $size)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 获取用户订单流水
     */
    public static function getUserOrders($uid, $page, $size)
    {
        return tOrders::where(self::USERKEY, $uid)
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

}
