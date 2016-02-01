<?php namespace App\Http\Controllers\Api;

use App\Trades\Transaction as tTransaction;
use App\Trades\User as tUser;
use Log;

class MoneyHookController extends ControllerBase {

    public $_allow = '*';
    public $_event = '';

    public function __construct() {
        parent::__construct();

        $str   = file_get_contents("php://input");
        Log::info('php://input', array($str));

        $this->_event = json_decode($str);

        if (!isset($this->_event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            return error('TRADE_CALLBACK_FAILED');
        }
    }

    public function chargeAction() {
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
            $trade = tTransaction::updateTrade($trade_no, $callback_id, $app_id, $out_trade_no, tTransaction::STATUS_NORMAL, $amount, $refund_url, $time_paid, $time_expire);

            $open_id        = isset($trade->attach->openid)?$trade->attach->openid: '';

            tUser::addBalance($trade->uid, $amount, $trade->subject.'-'.$trade->body, $open_id);

            return $this->output();
        }
        return error('TRADE_CALLBACK_FAILED');
    }

    public function transferAction() {
        if( $this->_event->type == "transfer.succeeded") {
            $data = $this->_event->data->object;

            $callback_id    = $data->id;
            $app_id         = $data->app;
            $trade_no       = $data->order_no;
            $amount         = $data->amount;
            $out_trade_no   = $data->transaction_no;
            $refund_url     = null;
            $time_paid      = $data->time_transferred;

            $trade = tTransaction::updateTrade($trade_no, $callback_id, $app_id, $out_trade_no, tTransaction::STATUS_NORMAL, $amount, $refund_url, $time_paid, $time_paid);
            if(isset($trade->attach->account_id)) {
                tAccount::udpateStatus($trade->attach->account_id, tAccount::STATUS_NORMAL);
            }
            return $this->output();
        }
        return error('TRADE_CALLBACK_FAILED');
    }

    public function redAction() {
        
        $data = $this->_event->data->object;

        $callback_id    = $data->id;
        $trade_no       = $data->order_no;
        $app_id         = $data->app;
        $amount         = $data->amount;
        $out_trade_no   = $data->transaction_no;
        $refund_url     = null;
        $time_paid      = $data->created;


        if( $this->_event->type == "red_envelope.sent") {
            $trade = tTransaction::updateTrade($trade_no, $callback_id, $app_id, $out_trade_no, tTransaction::STATUS_UNCERTAIN, $amount, $refund_url, $time_paid, $time_paid);

            return $this->output();
        }
        else if( $this->_event->type == "red_envelope.received") {
            $trade = tTransaction::updateTrade($trade_no, $callback_id, $app_id, $out_trade_no, tTransaction::STATUS_NORMAL, $amount, $refund_url, $time_paid, $time_paid);

            return $this->output();
        }
        return error('TRADE_CALLBACK_FAILED');
    }
}
