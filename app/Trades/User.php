<?php namespace App\Trades;

use App\Models\User as mUser;
use App\Trades\Account as tAccount;

class User extends TradeBase
{
    public $table = 'users';
    const SYSTEM_USER_ID = 1;


    /**
     * 设置冻结金额
     */
    public static function setFreezing($uid, $freezing)
    {
        if (!is_double($freezing)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
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
        if (!is_double($balance)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
        $user = mUser::where('uid', $uid)->first();

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
    public static function addBalance($uid, $amount)
    {
        $balance = self::getBalance($uid);
        $balance = ($balance + $amount);
        self::setBalance($uid, $balance);
        return $balance;
    }

    /*
     * 检查扣除商品费用后,用户余额是否充足
     * */
    public static function checkBalance($uid, $amount)
    {
        $balance = self::getBalance($uid);
        $balance = ($balance - $amount);
        if (0 > $balance) {
            return false;
        }
        return true;
    }


    /*
     * 冻结金额
     */
    public static function freezeBalance($uid, $amount)
    {
        //扣除用户余额
        self::subduceBalance($uid, $amount);
        //设置冻结
        self::addFreezing($uid, $amount);
    }

    /*
     * 解除冻结
     */
    public static function unFreezeBalance($uid, $amount)
    {
        //扣除冻结金额
        self::subduceFreezing($uid, $amount);
        //解冻以后回退到余额
        self::addBalance($uid, $amount);

    }

    /*
     * 扣除用户金额
     */
    public static function subduceBalance($uid, $amount)
    {
        $balance = self::getBalance($uid);
        $balance = ($balance - $amount);
        self::setBalance($uid, $balance);
        return $balance;
    }

    /*
     * 增加冻结金额
     */
    public static function addFreezing($uid, $amount)
    {
        $freezing = self::getFreezing($uid);
        $freezing = ($freezing + $amount);
        self::setFreezing($uid, $freezing);
        return $freezing;
    }

    /*
     * 扣除冻结金额
     */
    public static function subduceFreezing($uid, $amount)
    {
        $freezing = self::getFreezing($uid);
        $freezing = ($freezing - $amount);
        self::setFreezing($uid, $freezing);
        return $freezing;
    }

    /**
     * 获取账户流水记录
     */
    public static function getUserAccounts($uid)
    {
    }

    /**
     * 获取用户订单流水
     */
    public static function getUserOrders($uid)
    {
    }

    public static function pay($uid, $sellerUid, $amount)
    {
        //扣除购买人金额
        $userGoodsBalance = self::subduceBalance($uid, $amount);
        tAccount::freezeAccount($uid, $amount, $userGoodsBalance, tAccount::STATUS_ACCOUNT_SUCCEED, '付款成功');

        //增加卖家余额
        $sellerBalance = self::addBalance($sellerUid, $amount);
        tAccount::freezeAccount($sellerUid, $amount, $sellerBalance, tAccount::STATUS_ACCOUNT_SUCCEED, '作品收入');
    }


    public function __construct()
    {

    }
}
