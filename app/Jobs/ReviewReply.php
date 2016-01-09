<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Services\Reply as sReply;
use App\Services\Review as sReview;
use App\Services\ThreadCategory as sThreadCategory;
use App\Models\Review as mReview;


class ReviewReply extends Job
{
    public $condition   = array();

    public $sender_uid;
    public $upload_id;
    public $ask_id;
    public $desc;
    public $rid;
    public $category_ids;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $rid, $uid, $ask_id, $upload_id, $desc, $category_ids )
    {
        $this->rid = $rid;
        $this->desc = $desc;
        $this->ask_id = $ask_id;
        $this->sender_uid = $uid;
        $this->upload_id = $upload_id;
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
        if( $review->status == mReview::STATUS_READY
            && $this->sender_uid && $this->ask_id && $this->upload_id && $this->desc
        ){
            $reply = sReply::addNewReply(
                $this->sender_uid,
                $this->ask_id,
                $this->upload_id,
                $this->desc
            );
            sReview::updateStatus([$this->rid], mReview::STATUS_NORMAL);
            if( $this->category_ids ){
                sThreadCategory::addCategoryToThread( $this->sender_uid, mReview::TYPE_ASK, $reply->id, $this->category_ids, mReview::STATUS_NORMAL );
            }
            else{
                sThreadCategory::addNormalThreadCategory( $this->sender_uid, mReview::TYPE_ASK, $reply->id );
            }
        }
        if( $this->attempts() > 3 ){
            sReview::updateStatus([$this->rid], mReview::STATUS_REJECT);
            $this->delete();
        }
    }


}
