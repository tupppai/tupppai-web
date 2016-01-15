<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Trades\Account as tAccount;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
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
    public function __construct($askId, $replyId, $uid, $sellerUid)
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

        $isAskFirstReplyThreeDay = sAsk::isAskFirstReplyXDay($this->askId, 3);
        if (!$isAskFirstReplyThreeDay) {

        }

    }


}
