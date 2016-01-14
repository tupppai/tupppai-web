<?php namespace App\Trades;

use App\Services\User as sUser;

class User extends TradeBase
{
    public $table = 'users';

    /**
     * 设置账户余额
     */
    public static function setBalance($uid, $balance)
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
        $user = sUser::getUserByUid($uid);

        $user->balance = $balance*1000;
        $user->save();
    }

    /**
     * 获取用户余额
     */
    public static function getBalance($uid)
    {
        $balance = sUser::getUserBalance($uid);
        return $balance / 1000;
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

    /**
     * 设置冻结金额
     */
    public static function setFreezing($uid, $amount)
    {
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
    public static function freezeBalance($uid, $amount)
    {
        $userGoodsBalance  = self::getUserGoodsBalance($uid, $amount);
        self::setBalance($uid, $userGoodsBalance);
        //设置冻结
        $userGoodsFreezing = self::getUserGoodsFreezing($uid, $amount);
        self::setFreezing($uid, $userGoodsFreezing);
    }

    /*
     * 计算用户购买商品后余额
     */
    public static function getUserGoodsBalance($uid, $amount)
    {
        //设置余额
        $userGoodsBalance = self::getBalance($uid);
        $userGoodsBalance = ($userGoodsBalance - $amount);
        return $userGoodsBalance;
    }

    /*
     * 计算用户购买商品后冻结金额
     */
    public static function getUserGoodsFreezing($uid, $amount)
    {
        $userGoodsFreezing = self::getFreezing($uid);
        $userGoodsFreezing = ($userGoodsFreezing + $amount);
        return $userGoodsFreezing;
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


}
