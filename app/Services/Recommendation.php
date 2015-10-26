<?php
namespace App\Services;

use App\Models\Recommendation as mRecommendation;

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

	public static function getPendingRecByRoleId( $role_id ){
		return self::getRecommendationsByRoleId( $role_id, mRecommendation::STATUS_CHECKED );
	}

	public static function addNewRec( $introducer_uid, $uid, $role_id, $reason ){
		$mRec = new mRecommendation();
		return $mRec->add( $introducer_uid, $uid, $role_id, $reason );
	}
}
