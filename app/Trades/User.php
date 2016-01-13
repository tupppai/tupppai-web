<?php namespace App\Trades\Models;

use App\Services\User as sUser;

class User extends ModelBase {
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
}
