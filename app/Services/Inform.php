<?php

namespace App\Services;

use App\Services\ActionLog  as sActionLog;
use App\Services\User       as sUser;
use App\Services\Ask        as sAsk;
use App\Services\Reply      as sReply;
use App\Services\Comment      as sComment;

use App\Models\Inform  as mInform;
use App\Models\User    as mUser;
use App\Models\Ask     as mAsk;
use App\Models\Reply   as mReply;
use App\Models\Comment as mComment;
use App\Models\Count   as mCount;


class Inform extends ServiceBase {

	private static function checkTargetByTypeAndId( $target_type, $target_id ){
		switch( $target_type ){
			case mInform::TYPE_ASK:
                $ask = sAsk::getAskById( $target_id, false);
				break;
			case mInform::TYPE_REPLY:
				$reply = sReply::getReplyById( $target_id );
				break;
			case mInform::TYPE_COMMENT:
				$comment = sComment::getCommentById( $target_id );
				break;
			case mInform::TYPE_USER:
				$user = sUser::getUserByUid( $target_id );
                if( !$user ){
                    return error('USER_NOT_EXIST');
				}
				break;
			default:
				return false;
				break;
		}
		return true;
	}

	private static function increaseInformCount( $uid, $target_type, $target_id ){
		$ret = NULL;
		switch( $target_type ){
			case mInform::TYPE_ASK:
                sActionLog::init('INFORM_ASK');
				$ret = sAsk::updateAskCount($target_id, 'inform', mCount::STATUS_NORMAL);
				break;
			case mInform::TYPE_REPLY:
                sActionLog::init('INFORM_REPLY');
				$ret = sReply::updateReplyCount($target_id, 'inform', mCount::STATUS_NORMAL);
				break;
			case mInform::TYPE_COMMENT:
                sActionLog::init('INFORM_COMMENT');
				$ret = sComment::updateCommentCount($target_id, 'inform', mCount::STATUS_NORMAL);
				break;
			case mInform::TYPE_USER:
				// $res = Count::inform($uid, $target_id, Count::TYPE_USER);
				// if($res == 1) {
		        // $msg    = 'okay!';
		        //       $res = true;
		        //     User::count_add($target_id, 'inform');
		        // }
				break;
		}
        sActionLog::save($ret);
		return $ret;
	}

	public static function report( $uid, $target_type, $target_id, $content ){
		$report = new mInform();
		sActionLog::init('REPORT_ABUSE',$report);

		self::checkTargetByTypeAndId( $target_type, $target_id );


		$content=  trim($content);
		if( !$content ){
			return error('INFORM_EMPTY_CONTENT');
		}
		// todo: configurize
		define('CONTENT_MIN_LENGTH', 15);
		define('CONTENT_MAX_LENGTH', 5000);

		if( mb_strlen($content) <  CONTENT_MIN_LENGTH && mb_strlen($content) > CONTENT_MAX_LENGTH ){
			return error('INFORM_CONTENT_LENGTH_ERR');
		}

		$prev = $report->get_pending_inform_by( $uid, $target_type, $target_id );
		if( $prev ){
			if( $prev->content == $content ){
				return self::brief($prev); //511 重复举报相同内容
			}
			$prev->status = mInform::INFORM_STATUS_REPLACED;
			$prev->save();
		}

		$report->assign(array(
			'uid'         => $uid,
			'target_type' => $target_type,
			'target_id'   => $target_id,
			'content'     => $content
		));
		$report->save();
		sActionLog::save($report);

		self::increaseInformCount( $uid, $target_type, $target_id );

		return self::brief($report);
	}

	public static function dealReport( $id, $uid, $result, $status = mInform::INFORM_STATUS_SOLVED ){
		$mInform = new mInform();

		$report = $mInform->get_inform_by_id( $id );
		if( $report->status != $this::INFORM_STATUS_PENDING ){
			return false;
		}
        sActionLog::init('DEAL_INFORM', $report);

		$report ->assign(array(
			'status'      => $status,
			'oper_time'   => time(),
			'oper_by'     => $uid,
			'oper_result' => $result
		));

		$report->save();
        sActionLog::save( $report);
		return self::brief($report);
    }

	public static function brief( $informObj ){
		$obj = array(
			'id' => $informObj->id,
			'create_time' => $informObj->create_time
		);
		return $obj;
    }
}
