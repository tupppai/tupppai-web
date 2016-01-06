<?php namespace App\Services;

use App\Models\PermissionRole as mPermissionRole;
use App\Services\ActionLog as sActionLog;


class PermissionRole extends ServiceBase
{

    /**
     * 通过role获取权限列表
     */
    public static function getPermissionsByRoleId($role_id){
        $permissions = (new mPermissionRole)->get_permissions_by_role_id($role_id);
        $permission_ids = array();
        foreach($permissions as $row){
            $permission_ids[] = $row->permission_id;
        }

        return implode(",", $permission_ids);
    }

    /**
     * 更新角色的权限
     * @param  [integer] $role_id      角色id
     * @param  [mixed] $permission_ids  可传单个id，或数组
     * @return [boolean]                返回boolean
     */
    public static function updatePermissions( $role_id, $permission_ids ){
        if( empty( $role_id) || !is_numeric($role_id) ){
            return error('EMPTY_ROLE_ID');
        }

        if( !is_array($permission_ids) ){
            $permission_ids = explode(',', $permission_ids);
        }
        if( empty($permission_ids) ){
            return error('EMPTY_PERMISSION_ID');
        }

        $per_role_model = new self();

        $pers = self::getPermissionsByRoleId( $role_id );
        $pers = explode(',', $pers);

        $add_pers = array_filter( array_diff( $permission_ids, $pers ) );
        $del_pers = array_filter( array_diff( $pers, $permission_ids ) );

        //add previleges
        foreach( $add_pers as $key => $per_id ){
            sActionLog::init( 'ADD_PREVILEGE' );
            $pre = new mPermissionRole();
            $pre->role_id= $role_id;
            $pre->permission_id = $per_id;
            $pre->save();
            sActionLog::save( $pre );
        }

        ##skys todo:
        if(!empty($del_pers)){
            sActionLog::init('REVOKE_PRIVILEGE' );
            $pre = new mPermissionRole();
            $pre = $pre->where('role_id', $role_id)
                ->whereIn('permission_id', $del_pers)
                ->delete();
            sActionLog::log( $pre );
        }
        return true;
    }
}
