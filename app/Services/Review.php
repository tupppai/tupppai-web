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

    public static function updateStatus($review_ids, $status, $data="")
    {
        $mReview = new mReview();

        foreach( $review_ids as $review_id ){
            $review = $mReview->where('id', $review_id)->firstOrFail();

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

            sActionLog::init( 'MODIFY_REVIEW_STATUS' );
            $res = $review->save();
            sActionLog::save( $res );
        }

        return true;
    }
}
