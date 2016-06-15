<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Services\Reply as sReply;
use App\Models\Reply as mReply;

class UpReply extends Job
{
    public $condition   = array();
    const COMMENT_CLICK_RATE = 30;

    public $sender_uid;
    public $target_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $target_id, $sender_uid  )
    {
        $this->sender_uid  = $sender_uid;
        $this->target_id   = $target_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if( $this->attempts() > 3 ){
            $this->delete();
        }
        $ret = sReply::upReply( $this->target_id, mReply::STATUS_NORMAL, $this->sender_uid );

        $clickAmount = mt_rand( (int)self::COMMENT_CLICK_RATE*0.9, (int)self::COMMENT_CLICK_RATE*1.1 );

        \App\Counters\ReplyCounts::inc( $this->target_id, 'click', $clickAmount );
    }

}
