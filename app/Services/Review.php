<?php
namespace App\Services;

use App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Label as mLabel,
    App\Models\Upload as mUpload,
    App\Models\Review as mReview;

use App\Services\Label as sLabel,
    App\Services\Reply as sReply,
    App\Services\Ask as sAsk,
    App\Services\ActionLog as sActionLog;
use Queue;
use App\Jobs\ReviewAsk as jReviewAsk;
use App\Jobs\ReviewReply as jReviewReply;
use Carbon\Carbon;


class Review extends ServiceBase{

    public static function addNewAskReview($upload_id, $labels)
    {
        $review = new mReview;
        $review->assign(array(
            'upload_id' => $upload_id,
            'labels'    => $labels,
            'status'    => mReview::STATUS_HIDDEN
        ));

        sActionLog::init( 'ADD_REVIEW' );
        $res = $review->save();
        sActionLog::save( $res );
        return $res;
    }

    public static function addNewReplyReview($review_id, $ask_id, $uid, $puppet_uid, $upload_id, $labels, $release_time)
    {
        $review = new mReview;
        $review->assign(array(
            'ask_id'    => $ask_id,
            'review_id' => $review_id,
            'upload_id' => $upload_id,
            'labels'    => $labels,
            'uid'       => $uid,
            'parttime_uid' => $puppet_uid,
            'release_time' => $release_time,
            'type'      => mReview::TYPE_REPLY,
            'status'    => mReview::STATUS_HIDDEN
        ));

        sActionLog::init( 'ADD_REVIEW' );
        $res = $review->save();
        sActionLog::save( $res );
        return $res;
    }

    public static function getReviewByid($id) {
        return (new mReview)->get_review_by_id($id);
    }

    public static function updateReviewStatus($id, $status) {
        $mReview = new mReview();
        $review = $mReview->get_review_by_id($id);
        $review->status = $status;
        //todo: action log
        return $review->save();
    }

    public static function updateStatus($review_ids, $status, $data="")
    {
        $mReview = new mReview();

        foreach( $review_ids as $review_id ){
            $review = $mReview->where('id', $review_id)->firstOrFail();

            $review->status = $status;
            switch($status){
                case mReview::STATUS_NORMAL:
                    $review->score = $data;
                    break;
                case mReview::STATUS_REJECT:
                    $review->evaluation = $data;
                    break;
                // case mReview::STATUS_RELEASE:
                //     //logger about release
                //     break;
                case mReview::STATUS_DELETED:
                    break;
            }

            sActionLog::init( 'MODIFY_REVIEW_STATUS' );
            $res = $review->save();
            sActionLog::save( $res );

            if( $status == mReview::STATUS_READY ){
                $time = Carbon::createFromTimestamp( $res['release_time'] );
                if( $review->type == 1 ){
                    Queue::later( $time, new jReviewAsk( $review_id, $res['parttime_uid'], [$res['upload_id']], $res['labels'] ) );
                }
                else{
                    Queue::later( $time, new jReviewReply( $review_id, $res['parttime_uid'], $res['ask_id'], $res['upload_id'], $res['labels'] ) );
                }
            }
        }

        return true;
    }

    public static function updateReview( $id, $release_time, $parttime_uid, $labels ){
        $values = [
            'release_time' => $release_time,
            'parttime_uid' => $parttime_uid,
            'labels'         => $labels
        ];
        sActionLog::init( 'UPDATE_REVIEW' );
        $r = (new mReview)->where( 'id', $id )->update( $values );
        sActionLog::save( $r );
        return $r;
    }
}
