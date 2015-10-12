<?php namespace App\Services;
use Html;

use App\Services\ActionLog as sActionLog;
use App\Services\UserRole as sUserRole;
use App\Services\Role as sRole;
use App\Services\User as sUser;

use App\Models\Puppet as mPuppet;
use App\Models\User as mUser;
use App\Models\Role as mRole;

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
            $row->roles = '无';
            $roles = sUserRole::getRoleStrByUid( $row->uid );
            $role_names = [];
            if( $roles ){
                foreach( $roles as $role ){
                    if( $role != mRole::ROLE_HELP || $role != mRole::ROLE_WORK ){
                        $r = sRole::getRoleById( $role );
                        $role_names[] = $r['display_name'];
                    }
                }

                $row->roles = implode(',', $role_names);
            }


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

    public static function getPuppets( $uid, $roles = [] ){
        $puppets =  ( new mPuppet )->get_puppets( $uid, $roles );
        $puppet_list = [];
        foreach( $puppets as $puppet ){
            $puppet_list[] = sUser::getUserByUid( $puppet->puppet_uid );
        }

        return $puppet_list;
    }


	public static function editProfile( $owner, $uid, $profile ){
		$mPuppet = new mPuppet();
		$mUser = new mUser();

		$owner_uid = $mPuppet->where( 'puppet_uid', $uid )->pluck('owner_uid');

		if( $owner_uid && $owner != $owner_uid ){
			return error( 'WRONG_OWNER' );
		}

        if( $mUser->where('nickname', $profile['nickname'])->where('uid','!=', $uid )->exists( ) ){
            return error( 'NICKNAME_EXISTS', '该昵称已被注册');
        }

        if( is_null( $profile['phone'] ) ){
            $a = $mUser->count( );
            $profile['phone'] = config('global.PHONE_BASE') + $a;
        }

        sActionLog::init( 'REGISTER' );
		$user = $mUser->updateOrCreate( ['uid'=>$uid], $profile );
        sActionLog::save( $user );

        if( $profile['roles'] ){
            sUserRole::assignRole( $user->uid, $profile['roles'] );
        }

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
