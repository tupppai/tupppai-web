<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Trades\User;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;


class CheckAskHasReply extends Job
{
    public $askId;
    public $replyId;
    public $uid;
    public $sellerUid;
    public $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($askId, $amount)
    {
        $this->askId = $askId;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //获取商品金额
            $amount = $this->amount;

            //第一个作品在三天以内没有出现
            if (sAsk::isAskHasFirstReplyXDay($this->askId, 3)) {
                User::unFreezeBalance($this->uid, $amount);
            }
        } catch (\Exception $e) {
            Log::error('CheckAskHasReply', array($e->getMessage()));
        }
    }


}
