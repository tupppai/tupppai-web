<?php
namespace App\Http\Controllers\Main2;

use App\Trades\User as tUser;
use App\Trades\Transaction as tTransaction;

use App\Services\Ask as sAsk;
use App\Services\Comment as sComment;
use App\Services\Reward as sReward;
use App\Services\Reply as sReply;

use App\Models\Reward as mReward;

use Log;
use DB;
use Queue;

class HookController extends ControllerBase{
	public $_allow = '*';
    public $_event = '';

    public function __construct() {
        parent::__construct();

        $str   = file_get_contents("php://input");

        $this->_event = json_decode($str);

        if (!isset($this->_event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            return error('TRADE_CALLBACK_FAILED');
        }
    }

    public function charge() {
        if( $this->_event->type == "charge.succeeded") {
            Log::info('charge', array($this->_event));
            $data = $this->_event->data->object;

            $callback_id    = $data->id;
            $trade_no       = $data->order_no;
            $app_id         = $data->app;
            $amount         = $data->amount;
            $out_trade_no   = $data->transaction_no;
            $refund_url     = $data->refunds->url;
            $time_paid      = $data->time_paid;
            $time_expire    = $data->time_expire;

            try {
                DB::beginTransaction();
                $trade = tTransaction::updateTrade($trade_no, $callback_id, $app_id, $out_trade_no, tTransaction::STATUS_NORMAL, $amount, $refund_url, $time_paid, $time_expire);

                $open_id        = isset($trade->attach->openid)?$trade->attach->openid: '';
                tUser::addBalance($trade->uid, $amount, $trade->subject.'-'.$trade->body, $open_id);

                Log::info('trade' ,array($trade));
                if(isset($trade->attach->action) ){
					if( $trade->attach->action=='reward'){
						//打赏
			            $reward = sReward::createReward($trade->uid, $trade->attach->target_type, $trade->attach->target_id ,$amount, '打赏.'.$trade->attach->comment);
			            if(!$reward) {
			                return error('TRADE_PAY_ERROR', '打赏失败');
			            }
						if($trade->attach->target_type == mReward::TYPE_ASK ) {
		                    $ask = sAsk::getAskById($trade->attach->target_id);
		                    tUser::pay($trade->uid, $ask->uid, $amount, '打赏');
		                }
		                else if( $trade->attach->target_type == mReward::TYPE_REPLY ){
							//支付打赏的回调逻辑
							$reply = sReply::getReplyById($trade->attach->target_id);
		                    tUser::pay($trade->uid, $reply->uid, $amount, '打赏');
						}
						//留言 评论
				        $comment = sComment::addNewComment($trade->uid, $trade->attach->comment, $trade->attach->target_type, $trade->attach->target_id);
				    }
                }
                else {
                    // 打赏不能用充值的推送
                    Queue::push(new jPush(array(
                         'uid'=>$trade->uid,
                         'type'=>'self_recharge',
                         'amount' => money_convert( $amount )
                    )));
                }
                DB::commit();

                return $this->output();
            } catch(\Exception $e) {
                DB::rollback();
                Log::info('transfer err', array($e));
            }
        }
        return error('TRADE_CALLBACK_FAILED');
    }

    public function transfer() {
        if( $this->_event->type == "transfer.succeeded") {
            $data = $this->_event->data->object;

            $callback_id    = $data->id;
            $app_id         = $data->app;
            $trade_no       = $data->order_no;
            $amount         = $data->amount;
            $out_trade_no   = $data->transaction_no;
            $refund_url     = null;
            $time_paid      = $data->time_transferred;

            try {
                DB::beginTransaction();
                $trade = tTransaction::updateTrade($trade_no, $callback_id, $app_id, $out_trade_no, tTransaction::STATUS_NORMAL, $amount, $refund_url, $time_paid, $time_paid);
                if(isset($trade->account_id)) {
                    tAccount::updateStatus($trade->account_id, tAccount::STATUS_NORMAL);
                }
                DB::commit();

                return $this->output();
            } catch(\Exception $e) {
                DB::rollback();
                Log::info('transfer err', array($e));
            }
        }
        return error('TRADE_CALLBACK_FAILED');
    }

}