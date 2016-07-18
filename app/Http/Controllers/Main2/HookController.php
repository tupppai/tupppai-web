<?php
namespace App\Http\Controllers\Main2;

use App\Trades\User as tUser;
use App\Trades\Transaction as tTransaction;

use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Comment as sComment;
use App\Services\Reward as sReward;
use App\Services\Reply as sReply;
use App\Services\WXMsg as sWXMsg;
use App\Services\UserLanding as sUserLanding;

use App\Models\Reward as mReward;
use App\Models\UserLanding as mUserLanding;

use App\Jobs\Push as jPush;

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
                        $author_uid = 0;
                        $jumpUrl = '';

						if($trade->attach->target_type == mReward::TYPE_ASK ) {
		                    $ask = sAsk::getAskById($trade->attach->target_id);
                            $author_uid = $ask->uid;
                            $jumpUrl = '/services/?/#detail/works/'.$ask->id;
                        }
                        else if( $trade->attach->target_type == mReward::TYPE_REPLY ){
                            //支付打赏的回调逻辑
                            $reply = sReply::getReplyById($trade->attach->target_id);
                            $author_uid = $reply->uid;
                            $jumpUrl = '/services/?/#detail/detail/2/'.$reply->id;
                        }
                        tUser::pay($trade->uid, $author_uid, $amount, '打赏');

                        $commentHead = '打赏了'.number_format($amount/100,2).'元，并说：';
						//留言 评论
				        $comment = sComment::addNewComment($trade->uid, $commentHead.$trade->attach->comment, $trade->attach->target_type, $trade->attach->target_id);
                        $guest = sUser::getUserByUid( $trade->uid );
                        $author = sUser::getUserByUid( $author_uid );
                        $authorUserLanding = sUserLanding::getUserLandingByUid( $author_uid, mUserLanding::TYPE_WEIXIN_MP );
                        if( $authorUserLanding ){
                            // 打赏成功发送微信模板消息
                            $tplVars = [
                                'first'=>'禀告大神：小热粉“'.$author->username.'”给您进贡了'.number_format($amount/100,2).'元，赶紧戳这里临幸一下他吧~',
                                'keyword1' => number_format($amount/100,2).'元',
                                'keyword2' => $trade->trade_finish_time,
                                'keyword3' => '图派打赏',
                                'keyword4' => $trade->attach->comment,
                                'remark' => '贡品已打到您的图派账户，请到图派个人主页查看小金库吧~~被赞，评论，分享作品，打赏等都会增加您作品的曝光度，帮你上13区（热门），接受更多人的膜拜哦~~'
                            ];
                            sWXMsg::sendMsg( sWXMsg::TPL_ID_HAS_NEW_REWARD, $tplVars, [$authorUserLanding->openid], $jumpUrl );
                        }
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
