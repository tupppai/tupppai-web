<?php namespace App\Services;
use Html;

use App\Services\ActionLog as sActionLog;

use App\Models\Puppet as mPuppet;
use App\Models\User as mUser;

class Puppet extends ServiceBase{
	public static function getPuppetList( $uid, $cond ){
		$puppet = new mPuppet();

        $_REQUEST['sort'] = "create_time desc";

        $data  = $puppet->list_puppets( $uid, $cond );

        foreach($data as $row){
        	$row->uid = $row->user->uid;
        	$row->phone = $row->user->phone;
        	$row->nickname = $row->user->nickname;
            $row->sex = get_sex_name($row->user->sex);
            $row->avatar = $row->user->avatar ? '<img class="user-portrait" src="'.$row->user->avatar.'" />':'无头像';
            $row->create_time = date('Y-m-d H:i', $row->user->create_time);

            $row->oper   = Html::link('#', '编辑', array(
                'class'=>'edit'
            ));
        }

        $results =  array(
            'data' => $data,
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total()
        );

        return $results;
	}

    public static function getPuppets( $uid ){
        return ( new mPuppet )->get_puppets( $uid );
    }

	public static function editProfile( $owner, $uid, $profile ){
		$mPuppet = new mPuppet();
		$mUser = new mUser();

		$owner_uid = $mPuppet->where( 'puppet_uid', $uid )->pluck('owner_uid');

		if( $owner != $owner ){
			return error( 'WRONG_OWNER' );
		}

        if( $mUser->where('nickname', $profile['nickname'])->where('uid','!=', $uid )->count( ) > 0 ){
            return error( 'NICKNAME_EXISTS', '该昵称已被注册');
        }

        if( is_null( $profile['phone'] ) ){
            $a = $mUser->max( 'uid' );
            $profile['phone'] = config('global.PHONE_BASE') + $a;
        }

        sActionLog::init( 'REGISTER' );
		$user = $mUser->updateOrCreate( ['uid'=>$uid], $profile );
        sActionLog::save( $user );

		return $user;
	}


    public static function updatePuppetRelationOf( $owner_uid, $puppet_uid ){
        $mPuppet = new mPuppet();
        $mUser = new mUser();
        if( !$mUser->find( $puppet_uid ) ){
            return error( 'USER_NOT_EXIST' );
        }

        $data = [
            'owner_uid' => $owner_uid,
            'puppet_uid' => $puppet_uid
        ];

        sActionLog::init( 'UPDATE_PUPPER_RELATION' );
        $p = $mPuppet->updateOrCreate( $data, $data );
        sActionLog::save( $p );

        return $p;
    }
}
