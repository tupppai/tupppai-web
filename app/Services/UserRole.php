<?php
namespace App\Services;

use \App\Models\UserRole as mUserRole,
    \App\Models\User as mUser;

use \App\Services\UserScheduling as sUserScheduling;
use App\Services\ActionLog as sActionLog;
use App\Services\Role as sRole;

class UserRole extends ServiceBase
{

    /**
     * 新添加用户
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $nickname 昵称
     * @param integer$phone    手机号码
     * @param string $email    邮箱地址
     * @param array  $options  其它。暂不支持
     */
    public static function addNewRelation($uid, $role_id)
    {
        sActionLog::init( 'ADD_NEW_RELATION' );
        $u = new mUserRole();
        $u->assign(array(
            'uid'=>$uid,
            'role_id'=>$role_id
        ));

        $ur = $u->save();

        sActionLog::save( $ur );
        return $ur;
    }

    /**
     * 检测用户权限
     * @param  [type] $uid        [用户ID]
     * @param  [type] $role_id    [权限ID]
     * @return [type] [description]
     */
    public static function checkAuth($uid, $role_id){
        $mUserRole = new mUserRole;

        if (is_array($role_id)){
            return !$mUserRole->get_user_roles_by_role_ids($uid, $role_id)->isEmpty();
        }
        else if( $role_id ){
            return !$mUserRole->get_user_roles_by_role_id($uid, $role_id)->isEmpty();
        }
        else{
            return false;
        }
    }

    /**
     * 获取相应类型的所有用户id
     */
    public static function getRolesById($role_id){
        return mUserRole::find("role_id = {$role_id} AND status=".mUserRole::STATUS_NORMAL);
    }

    /**
     * 通过roleids 获取users
     */
    public static function getRolesByIds($role_ids){
        $role_str = implode(',', $role_id);
        return mUserRole::find("role_id IN ({$role_str}) AND status=".mUserRole::STATUS_NORMAL);
    }

    /**
     * 通过uid获取角色列表
     */
    public static function getRoleStrByUid( $uid ){
        $mUserRole = new mUserRole;
        $roles = $mUserRole->get_user_roles_by_uid($uid);

        $roleids = array();
        foreach($roles as $role){
            $roleids[] = $role->role_id;
        }
        return $roleids;
    }

    public static function getRolesByUid( $uid ){
        $mUserRole = new mUserRole;
        $role_ids = $mUserRole->get_user_roles_by_uid($uid);

        $roles = array();
        foreach($role_ids as $role){
            $roles[] = sRole::detail( sRole::getRoleById( $role->role_id ) );
        }
        return $roles;
    }

    /**
     * 只获取第一个角色
     */
    public static function getFirstRoleIdByUid( $uid ){
        $mUserRole = new mUserRole;
        $role = $mUserRole->get_first_user_role_by_uid($uid);

        if(!$role) {
            return '';
        }

        return $role->role_id;
    }

    /**
     * 通过role id获取用户
     */
    public static function getUidsByIds($role_ids){
        $mUserRole = new mUserRole;
        $user_roles = $mUserRole->get_users_by_role_ids($role_ids);
        $uids = array();
        foreach($user_roles as $role){
            $uids[] = $role->uid;
        }
        return $uids;
    }

    /**
     * 通过role id获取用户
     */
    public static function getUsersByIds($role_ids){
        $mUserRole = new mUserRole;
        $user_roles = $mUserRole->get_users_by_role_ids($role_ids);
        $uids = array();

        $role_arr = array();
        foreach($user_roles as $role){
            $uids[] = $role->uid;

            $role_arr[$role->uid] = $role;
        }

        $mUser = new mUser;
        $users = $mUser->get_user_by_uids($uids);

        $data  = array();
        foreach($users as $user){
            $user->role_id = $role_arr[$user->uid]->role_id;
            $data[] = $user;
        }
        return $data;
    }

    /**
     * 计算角色数量
     */
    public static function countRolesById($role_id) {
        return (new mUserRole)->count_roles_by_id($role_id);
    }

    /**
     * 赋予权限
     * @param  [type] $uid      [用户id]
     * @param  [type] $role_ids [角色id]
     * @return [type]           [description]
     */
    public static function assignRole( $user_id, $role_ids ){
        if( empty($user_id) || !is_numeric($user_id) ){
            return error('EMPTY_UID', '请选择用户');
        }
        if( !is_array($role_ids) ){
            $role_ids = explode(',', $role_ids);
        }
        if( empty($role_ids) ){
            return error('EMPTY_ROLE_ID');
        }

        $roles = self::getRoleStrByUid($user_id);

        $add_roles = array_filter( array_diff( $role_ids, $roles ) );
        $del_roles = array_filter( array_diff( $roles, $role_ids ) );

        self::assignRoleToUser( $user_id, $add_roles );
        if(!empty($del_roles)){
            self::revokeRoleFromUser( $user_id, $del_roles );
        }
        return true;
    }

    public static function assignRoleToUser( $uid, $role_ids ){
        if( is_int($role_ids) ||is_string( $role_ids )){
            $role_ids = [$role_ids];
        }
        $mUserRole = new mUserRole();
        //add previleges
        sActionLog::init( 'ASSIGN_ROLE' );
        $pers = $mUserRole->assign_roles( $uid, $role_ids );
        sActionLog::save( $pers );
        return $pers;
    }

    public static function revokeRoleFromUser( $uid, $role_ids ){
        if( is_int($role_ids) ||is_string( $role_ids )){
            $role_ids = [$role_ids];
        }
        $mUserRole = new mUserRole();
        sActionLog::init('REVOKE_ROLE');
        $dels = $mUserRole->remove_roles( $uid, $role_ids );
        sActionLog::save( $dels );
        return $dels;
    }

    public static function checkUserHasRole( $uid, $role_id ){
        return (new mUserRole)->user_has_role_of( $uid, $role_id );
    }
    public static function checkUserIsStar( $uid ){
        return self::checkUserHasRole( $uid, mUserRole::ROLE_STAR );
    }
    public static function checkUserIsBlocked( $uid ){
        return self::checkUserHasRole( $uid, mUserRole::ROLE_BLOCKED );
    }
    public static function checkUserIsBlacklisted( $uid ){
        return self::checkUserHasRole( $uid, mUserRole::ROLE_BLACKLIST );
    }
    public static function checkUserIsPuppet( $uid ){
        return (
            self::checkUserHasRole( $uid, mUserRole::ROLE_HELP )
            || self::checkUserHasRole( $uid, mUserRole::ROLE_WORK )
            || self::checkUserHasRole( $uid, mUserRole::ROLE_CRITIC )
            );
    }
}
