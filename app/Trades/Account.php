<?php namespace App\Trades;

use App\Trades\Models\Account as mAccount;

class Account extends TradeBase {

    /**
     * 参数: id uid balance income_amount outcome_amount freeze_amount memo
     */
    public static function setAccount($uid, $memo = '') {
        $account = new Account;
        $account->uid = $uid;
        return $account->save();
    }

    /**
     * 设置余额 金钱都需要*1000
     */
    public static function setBalance($uid, $balance) {
        
        $account = (new mAccount)->get_account_by_uid($uid);
        $account->balance = $balance;
        return $account->save();
    }

    /**
     * 通过uid获取账户信息
     */
    public static function getAccountByUid($uid) {
        return (new mAccount)->get_account_by_uid($uid);
    }
}
