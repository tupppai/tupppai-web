<?php namespace App\Services;

use App\Services\ActionLog as sActionLog;
use App\Services\User as sUser;
use App\Services\UserRole as sUserRole;

use App\Models\Puppet as mPuppet;
use App\Models\User as mUser;
use App\Models\Role as mRole;

class Puppet extends ServiceBase{
    public static function getPuppetList( $uid, $cond ){

        #sky 涉及request的操作，都在controller完成，并且数据过滤筛选都在controller完成
        return (new mPuppet)->list_puppets( $uid, $cond);
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

        #sky 在service里面尽量少用laravel的方法咯，在model里面封装一层，毕竟其他地方可以复用
        #$owner_uid = $mPuppet->where( 'puppet_uid', $uid )->pluck('owner_uid');
        $puppet = $mPuppet->get_puppet_by_uid ($uid);
        #sky 因为这个方法有保存，所以索性把东西拿出来啦
        #if( $owner_uid && $owner != $owner_uid ){
        if( $puppet && $puppet->owner_uid != $owner ){
            return error( 'WRONG_OWNER' );
        }

        $user = sUser::getUserByNickname($profile['nickname']);
        #sky 尽量使用原本的代码咯~ 而且少用laravel的东西吧
        #if( $mUser->where('nickname', $profile['nickname'])->where('uid','!=', $uid )->exists( ) ){
        if($user && $user->uid != $uid) {
            return error( 'NICKNAME_EXISTS', '该昵称已被注册');
        }

        if( is_null( $profile['phone'] ) ){
            $a = $mUser->count( );
            $profile['phone'] = config('global.PHONE_BASE') + $a;
        }

        sActionLog::init( 'REGISTER' );
        #sky 这个不知道咋改 - - 一般应该先搜出来，然后判断有没有，没有的话就新建吧~
        #sky 那就先这样吧- -
        $user = $mUser->updateOrCreate( ['uid'=>$uid], $profile );
        #sky 看看试试这样,因为前面已经通过get_puppet_by_uid拿到对象了
        #$user = $user->assign($profile);
        #$user->save();
        sActionLog::save( $user );

        if( $profile['roles'] ){
            sUserRole::assignRole( $user->uid, $profile['roles'] );
        }

		return $user;
	}

    public static function updatePuppetRelationOf( $owner_uid, $puppet_uid, $status = mPuppet::STATUS_NORMAL ){
        $mPuppet = new mPuppet();
        #sky 尽量用有的service，没有的话就model，再没有就自己写model
        #$mUser = new mUser();
        #if( !$mUser->find( $puppet_uid ) ){
        if( !sUser::getUserByUid($puppet_uid) ) {
            return error( 'USER_NOT_EXIST' );
        }

        $puppet = $mPuppet->get_puppet($owner_uid, $puppet_uid);
        sActionLog::init( 'UPDATE_PUPPER_RELATION', $puppet_uid );
        #sky 这里绝壁有问题啦，看了一下数据库里面是id是主键，updateOrCreate会搜索主键的吧
        #按现在这个逻辑，每次都是新建吧，不会更新
        #$p = $mPuppet->updateOrCreate( $data, $data );
        if(!$puppet) {
            $puppet =  new mPuppet();
            $puppet->assign(array(
                'owner_uid' => $owner_uid,
                'puppet_uid' => $puppet_uid,
            ));
        }
        $puppet->status = $status;
        $puppet->save();

        sActionLog::save( $puppet );

        return $puppet;
    }
}
