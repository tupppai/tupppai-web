<?php namespace App\Handles\Trade;

use App\Events\Event;
use App\Models\Ask as mAsk;
use App\Services\User as sUser;
use App\Trades\Account as tAccount;
use App\Trades\User as tUser;
use Illuminate\Support\Facades\DB;


class Trade
{
    public function __construct()
    {

    }

    public function handle(Event $handle)
    {
        //This is Logic
    } 

    /*
     * 获取商品金额
     */
    public function getGoodsAmount($product)
    {
        return 0.5;
    }
    /*
     * 计算用户购买商品后余额
     */
    public static function getUserGoodsBalance($uid,$amount)
    {
        //设置余额
        $userGoodsBalance = tUser::getBalance($uid);
        $userGoodsBalance = ($userGoodsBalance-$amount);
        return $userGoodsBalance;
    }
    /*
     * 计算用户购买商品后冻结金额
     */
    public static function getUserGoodsFreezing($uid,$amount)
    {
        $userGoodsFreezing = tUser::getFreezing($uid);
        $userGoodsFreezing = ($userGoodsFreezing+$amount);
        return $userGoodsFreezing;
    } 
 

}
