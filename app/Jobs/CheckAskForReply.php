<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Trades\Account as tAccount;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
use App\Trades\User;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;

use App\Facades\Alidayu;
use App\Services\Sms as sSms;

class CheckAskForReply extends Job
{
    public $askId;
    public $replyId;
    public $uid;
    public $sellerUid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($askId)
    {
        $this->askId = $askId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //获取商品金额
        $amount = sProduct::getProductById(1);
        $amount = $amount['price'];

        $isAskFirstReplyThreeDay = sAsk::isAskFirstReplyXDay($this->askId, 3);
        //第一个作品在三天以内没有出现
        if (!$isAskFirstReplyThreeDay) {
            User::unFreezeBalance($this->uid,$amount);
        }

    }


}
