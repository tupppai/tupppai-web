<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Services\Ask as sAsk;
use App\Services\Review as sReview;
use App\Models\Review as mReview;


class ReviewAsk extends Job
{
    public $condition   = array();

    public $sender_uid;
    public $upload_ids;
    public $desc;
    public $rid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $rid, $uid, $upload_ids, $desc )
    {
        $this->rid = $rid;
        $this->desc = $desc;
        $this->sender_uid = $uid;
        $this->upload_ids = $upload_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $review = sReview::getReviewByid($this->rid);
        if( $review->status == mReview::STATUS_READY){
            $ask = sAsk::addNewAsk( $this->sender_uid, $this->upload_ids, $this->desc );
            sReview::updateStatus([$this->rid], mReview::STATUS_NORMAL, $ask->id);
            sReview::updateAskId( $this->rid, $ask->id );
        }
        if( $this->attempts() > 3 ){
            sReview::updateStatus([$this->rid], mReview::STATUS_REJECT);
            $this->delete();
        }
    }


}
