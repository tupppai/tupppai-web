<?php namespace App\Services;

use App\Services\ActionLog  as sActionLog;
use App\Services\User       as sUser;
use App\Services\Ask        as sAsk;
use App\Services\Reply      as sReply;
use App\Services\Comment    as sComment;
use App\Services\Usermeta   as sUsermeta;

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
        //todo counter
		switch( $target_type ){
			case mInform::TYPE_ASK:
                sActionLog::init('INFORM_ASK');
                $ret = sAsk::informAsk($target_id, mCount::STATUS_NORMAL);
				break;
			case mInform::TYPE_REPLY:
                sActionLog::init('INFORM_REPLY');
                $ret = sReply::informReply($target_id, mCount::STATUS_NORMAL);
				break;
			case mInform::TYPE_COMMENT:
                sActionLog::init('INFORM_COMMENT');
                $ret = sComment::informComment($target_id, mCount::STATUS_NORMAL);
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

	public static function dealReport( $id, $uid, $type ){
		$report = (new mInform)->get_inform_by_id( $id );
		if( !$report ){
			return error( 'INFORM_NOT_EXIST', '举报记录不存在');
		}
		if( $report->status != mInform::INFORM_STATUS_PENDING ){
			return '该举报已被处理。';
		}

		switch( $type ){
			case 'block_reporter':
				$ret = self::block_reporter($report->uid);
				if( $ret ){
					sActionLog::init('FORBID_USER');
					sActionLog::log( $ret );
				}
				$content = '已对举报者实施禁言。';
				$status = mInform::INFORM_STATUS_SOLVED;
				break;
			case 'block_author':
				$ret = self::block_author($report);
				if( $ret ){
					//获取以前的禁言设置？
					//如果用户不存在……
					sActionLog::init( 'FORBID_USER' );
					sActionLog::log( $ret );
				}
				$content = '已对被举报对象作者实施禁言。';
				$status = mInform::INFORM_STATUS_SOLVED;
				break;
			case 'ignore':
				$ret = true;//$this->ignore_report($report);
				$content = '已忽略该举报。';
				$status = mInform::INFORM_STATUS_IGNORED;
				break;
			case 'false_report':
				$ret = true;//$this->false_report($report);
				$content = '已标记该举报为误报。';
				$status = mInform::INFORM_STATUS_IGNORED;
				break;
			default:
				return $this->error( 'TYPE_NOT_EXIST', '不存在的处理类型' );
				break;
		}
		if( $ret ){
			$res = $report->deal_report($report->id, $uid, $content, $status);
			if( $res ){
				sActionLog::init( 'DEAL_INFORM', $report );
				sActionLog::log( $res );
			}else{
				$content = '处理失败';
			}
		}
		else{
			$content = '处理失败';
		}
		return $content;
	}


	public static function block_reporter($uid){
		$value = 60/*sec*/ * 60/*min*/ * 24/*hours*/ * 7/*days*/;

		if(!$uid) {
			return error( 'EMPTY_UID', '用户不存在');
		}
		$user = sUser::getUserByUid($uid);
		if(!$user) {
			return error( '', '用户不存在');
		}

		return sUsermeta::write_user_forbid($uid, $value);
	}

	public static function block_author($report){
		$value = 60/*sec*/ * 60/*min*/ * 24/*hours*/ * 7/*days*/;

		if( !$report ){
			return false;
		}

		$target_type = $report->target_type;
		$target_id = $report->target_id;

		switch( $target_type ){
			case mInform::TYPE_ASK:
				$ask = sAsk::getAskById( $target_id );
				if( !$ask ){
					return false;
				}
				$uid = $ask->uid;
				break;
			case mInform::TYPE_REPLY:
				$reply = sReply::getReplyById( $target_id );
				if( !$reply ){
					return false;
				}
				$uid = $reply->uid;
				break;
			case mInform::TYPE_COMMENT:
				$comment = sComment::getCommentById( $target_id );
				if( !$comment ){
					return false;
				}
				$uid = $comment->uid;
				break;
			case mInform::TYPE_USER:
				$uid = $target_id;
				break;
		}


		if(!$uid) {
			return error( '用户不存在' );
		}
		$user = sUser::getUserByUid($uid);
		if(!$user) {
			return error( '用户不存在' );
		}

		return sUsermeta::write_user_forbid($uid, $value);
	}

	public static function brief( $informObj ){
		$obj = array(
			'id' => $informObj->id,
			'create_time' => $informObj->create_time
		);
		return $obj;
    }

    public static function getInformById( $id ){
		return (new mInform)->get_inform_by_id( $id );
    }

    public static function countReportedTimesByUid( $uid ){
        return (new mInform)->sum_reported_times_by_uid( $uid );
    }

    public static function countReportTimes( $uid ){
		return (new mInform)->sum_report_times_by_uid( $uid );
    }

    public static function countTargetReportTimes( $target_type, $target_id ){
		return (new mInform)->count_target_report_times( $target_type, $target_id );
    }
}
