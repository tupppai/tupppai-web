<?php
namespace App\Models;

class UserRole extends ModelBase
{
    protected $table = 'user_roles';
    protected $guarded = ['id'];


    public function assign_roles( $uid, $role_ids ){
        $roles = [];
        foreach( $role_ids as $role_id ){
            $cond = [
                'uid' => $uid,
                'role_id' => $role_id
            ];
            $data = $cond;
            $data['status'] = self::STATUS_NORMAL;
            $role = $this->updateOrCreate( $cond, $data );
            $roles[] = $role;
        }

        return $roles;
    }

    public function remove_roles( $uid, $role_ids ){
        $user_roles = $this->where('uid', $uid)->valid()
                ->whereIn('role_id',$role_ids )
                ->update(['status'=>self::STATUS_DELETED]);
        return $user_roles;
    }
    public function count_roles_by_id($role_id) {
        return self::where('role_id', $role_id)
            ->where('status', self::STATUS_NORMAL)
            ->count();
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
        $user_roles = $this->where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->get();

        return $user_roles;
    }

    /**
     * 通过获取第一个角色
     */
    public function get_first_user_role_by_uid($uid) {
        $user_role = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->first();

        return $user_role;
    }

    /**
     * 通过role_id获取用户列表
     */
    public function get_users_by_role_ids($role_ids) {
        if( !is_array($role_ids) && is_numeric($role_ids) ){
            $role_ids = array($role_ids);
        }
        $user_roles = self::whereIn('role_id', $role_ids)
            ->where('status', self::STATUS_NORMAL)
            ->groupby('uid')
            ->get();

        return $user_roles;
    }

    public function user_has_role_of( $uid, $role_id ){
        return $this->where('uid', $uid)
                    ->where('role_id', $role_id)
                    ->where('status', self::STATUS_NORMAL)
                    ->exists();
    }
    //public static function check_authentication($uid, $role_id){
    //public static function get_role_users($role_id){
    //public static function assign_role( $user_id, $role_ids ){
    //public static function get_roles_by_user_id( $uid ){
}
