<?php
namespace App\Services;

use App\Models\Recommendation as mRecommendation;
use App\Services\UserRole as sUserRole;

class Recommendation extends ServiceBase
{
	public static function getRecommendationsByRoleId( $role_id, $status ){
		$mRec = new mRecommendation();
		$query = $mRec->with('user')
				->with('introducer')
				->where( [
					'role_id' => $role_id,
					'status' => $status
				]);
		$users = $query->get();
		$total = $query->count();
		return ['data'=> $users, 'total'=>$total];
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
			if( $status == mRecommendation::STATUS_NORMAL ){
				sUserRole::assignRoleToUser( $rec->uid, $rec->role_id );
			}
			else if( $status == mRecommendation::STATUS_DELETED || $status == mRecommendation::STATUS_REJECT){
				sUserRole::revokeRoleFromUser( $rec->uid, $rec->role_id );
			}
		}

		return true;
	}

	public static function getRecRoleIdByUid( $uid ){
		$mRec = new mRecommendation();
		return $mRec->where('uid', $uid)
				->whereNotIn('status', [
					mRecommendation::STATUS_DELETED,
					mRecommendation::STATUS_REJECT
				])
				->pluck('role_id');
	}
}
