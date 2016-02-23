<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Trades\User as tUser;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;


class CheckAskHasReply extends Job
{
    public $ask_id;
    public $uid;
    public $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ask_id, $amount, $uid)
    {
        $this->ask_id = $ask_id;
        $this->amount = $amount;
        $this->uid    = $uid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //第一个作品在三天以内如果没有出现
            if (!sAsk::isAskHasFirstReplyXDay($this->ask_id, 3)) {
                tUser::addBalance($this->uid, $this->amount, '入账，官方退款');
            }
        } catch (\Exception $e) {
            Log::error('CheckAskHasReply', array($e->getLine() . '------' . $e->getMessage()));
        }
    }


}
