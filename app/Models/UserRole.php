<?php
namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class UserRole extends ModelBase
{
    const SUPER_USER_UID = 1;

    const ROLE_HELP = 1;
    const ROLE_WORK = 2;
    const ROLE_PARTTIME = 3;
    const ROLE_STAFF    = 4;

    public function getSource()
    {
        return 'user_roles';
    }

    /**
     * 通过uid,role_ids获取关系
     */
    public function get_user_roles_by_role_ids($uid, $role_ids) {
        $user_roles = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->whereIn('role_id', $role_ids)
            ->get();

        return $user_roles;
    }

    /**
     * 通过uid,role_ids获取关系
     */
    public function get_user_roles_by_role_id($uid, $role_id) {
        $user_roles = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->where('role_id', $role_id)
            ->get();

        return $user_roles;
    }

    /**
     * 通过uid获取关系
     */
    public function get_user_roles_by_uid($uid) {
        $user_roles = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->get();

        return $user_roles;
    }
    
    /**
     * 通过role_id获取用户列表
     */
    public function get_users_by_role_ids($role_ids) {
        $user_roles = self::whereIn('role_id', $role_ids)
            ->where('status', self::STATUS_NORMAL)
            ->get();

        return $user_roles;
    }

    //public static function check_authentication($uid, $role_id){
    //public static function get_role_users($role_id){
    //public static function assign_role( $user_id, $role_ids ){
    //public static function get_roles_by_user_id( $uid ){
}
