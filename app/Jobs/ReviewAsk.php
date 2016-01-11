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
    public $category_ids;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $rid, $uid, $upload_ids, $desc, $category_ids )
    {
        $this->rid = $rid;
        $this->desc = $desc;
        $this->sender_uid = $uid;
        $this->upload_ids = $upload_ids;
        $this->category_ids = $category_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $review = sReview::getReviewByid($this->rid);
        if( $review->status == mReview::STATUS_READY && $this->sender_uid && $this->upload_ids && $this->desc){
            $ask = sAsk::addNewAsk( $this->sender_uid, $this->upload_ids, $this->desc );
            sReview::updateStatus([$this->rid], mReview::STATUS_NORMAL, $ask->id);
            sReview::updateAskId( $this->rid, $ask->id );
            if( $this->category_ids ){
                sThreadCategory::addCategoryToThread( $this->sender_uid, mReview::TYPE_ASK, $ask->id, $this->category_ids, mReview::STATUS_NORMAL );
            }
            else{
                sThreadCategory::addNormalThreadCategory( $this->sender_uid, mReview::TYPE_ASK, $ask->id );
            }
        }
        if( $this->attempts() > 3 ){
            sReview::updateStatus([$this->rid], mReview::STATUS_REJECT);
            $this->delete();
        }
    }


}
