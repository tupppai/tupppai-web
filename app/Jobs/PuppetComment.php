<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Services\Comment as sComment,
    App\Services\Message as sMessage;

use App\Models\Label as mLabel;


class PuppetComment extends Job
{
    public $condition   = array();
    const COMMENT_CLICK_RATE = 500;

    public $sender;
    public $content;
    public $target_id;
    public $target_type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $user_id, $content, $target_type, $target_id )
    {
        #参数
        $this->sender      = $user_id    ;
        $this->content     = $content    ;
        $this->target_id   = $target_id  ;
        $this->target_type = $target_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sender_uid  = $this->sender;
        $content     = $this->content;
        $target_id   = $this->target_id;
        $target_type = $this->target_type;

        sComment::addNewComment( $sender_uid, $content, $target_type, $target_id );
        $clickAmount = mt_rand( (int)self::COMMENT_CLICK_RATE*0.9, (int)self::COMMENT_CLICK_RATE*1.1 );
        if( $target_type == mLabel::TYPE_ASK ){
            \App\Counters\AskCounts::inc( $target_id, 'click', $clickAmount );
        }
        else if( $target_type == mLabel::TYPE_REPLY ){
            \App\Counters\ReplyCounts::inc( $target_id, 'click', $clickAmount );
        }
    }


}
