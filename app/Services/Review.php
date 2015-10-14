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

class Review extends ServiceBase{
    
    public static function addNewAskReview($upload_id, $labels)
    {
        $review = new mReview;
        $review->assign(array(
            'upload_id' => $upload_id,
            'labels'    => $labels,
            'status'    => mReview::STATUS_HIDDEN
        ));

        //todo: action log
        return $review->save();
    }
    
    public static function addNewReplyReview($review_id, $ask_id, $uid, $upload_id, $labels, $release_time)
    {
        $review = new mReview;
        $review->assign(array(
            'ask_id'    => $ask_id,
            'review_id' => $review_id,
            'upload_id' => $upload_id,
            'labels'    => $labels,
            'uid'       => $uid,
            'release_time' => $release_time,
            'type'      => mReview::TYPE_REPLY,
            'status'    => mReview::STATUS_READY
        ));

        //todo: action log
        return $review->save();
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

    public static function updateStatus($review, $status, $data="")
    {
        $review->status = $status;
        switch($status){
        case self::STATUS_NORMAL:
            $review->score = $data;
            break;
        case self::STATUS_REJECT:
            $review->evaluation = $data;
            break;
        case self::STATUS_RELEASE:
            //logger about release
            break;
        case self::STATUS_DELETED:
            break;
        }

        //todo action log
        return $review->save();
    }
}
