<?php
namespace App\Services;

use App\Models\Recommendation as mRecommendation;
use App\Services\UserRole as sUserRole;

class Recommendation extends ServiceBase
{
	public static function getRecommendationsByRoleId( $role_id, $status ){
		$mRec = new mRecommendation();
		$recs = $mRec->with('user')
				->with('introducer')
				->where( [
					'role_id' => $role_id,
					'status' => $status
				])
				->get();
		return $recs;
	}

	public static function getPassedRecByRoleId( $role_id ){
		return self::getRecommendationsByRoleId( $role_id, mRecommendation::STATUS_NORMAL );
	}

	public static function getInvalidRecByRoleId( $role_id ){
		return self::getRecommendationsByRoleId( $role_id, mRecommendation::STATUS_HIDDEN );
	}

	public static function getRejectedRecByRoleId( $role_id ){
		return self::getRecommendationsByRoleId( $role_id, mRecommendation::STATUS_REJECT );
	}

	public static function getCheckedRecByRoleId( $role_id ){
		return self::getRecommendationsByRoleId( $role_id, mRecommendation::STATUS_CHECKED );
	}

	public static function getPendingRecByRoleId( $role_id ){
		return self::getRecommendationsByRoleId( $role_id, mRecommendation::STATUS_READY );
	}

	public static function addNewRec( $introducer_uid, $uid, $role_id, $reason ){
		$mRec = new mRecommendation();
		return $mRec->add( $introducer_uid, $uid, $role_id, $reason );
	}

	public static function updateStatus( $uid, $ids, $status, $result = '' ){
		$mRec = new mRecommendation();

		foreach( $ids as $id ){
			$rec = $mRec->where('id', $id)->first();
			$rec->update_status( $uid, $status, $result );
			//todo::insert into user_role
			sUserRole::addNewRelation( $rec->uid, $rec->role_id );
		}

		return true;
	}
}
