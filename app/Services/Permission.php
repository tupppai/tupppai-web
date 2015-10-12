<?php
namespace App\Services;

use \App\Models\Permission as mPermission;
use App\Services\ActionLog as sActionLog;

class Permission extends ServiceBase
{
    /**
     * [save_permission 保存权限模块]
     * @param [type] $pid             [权限模块ID]
     * @param [type] $display_name    [模块名称]
     * @param [type] $controller_name [控制器名称]
     * @param [type] $action_name     [操作名称]
     */
    public static function addNewPermission( $display_name, $controller_name, $action_name){
        $mPermission = new mPermission();
        $existance =  $mPermission->where([
            'controller_name' => $controller_name,
            'action_name' => $action_name
        ])
        ->exists();
        if( $existance ){
            return error( 'PERMISSION_EXIST',  '模块已存在' );
        }


        $mPermission->display_name= $display_name;
        $mPermission->controller_name = $controller_name;
        $mPermission->action_name = $action_name;

        sActionLog::init( 'ADD_PERMISSION' );
        $permission = $mPermission->save();
        sActionLog::save( $permission );

        return $permission;
    }

    public static function updatePermission( $pid, $display_name, $controller_name, $action_name ){
        $mPermission = new mPermission();
        $per = $mPermission->where( 'id', $pid )->first( );
        if( !$per ){
            return error( 'PERMISSION_DOESNT_EXIST' );
        }

        sActionLog::init('EDIT_PERMISSION');
        $r = $per->update([
            'display_name' => $display_name,
            'controller_name' => $controller_name,
            'action_name' => $action_name
        ]);
        sActionLog::save( $r );

        return $r;
    }


    /**
     * [delete_permission 删除权限]
     * @return  boolean [删除是否成功]
     */
    public static function deletePermission($id){
        $mPermission = new mPermission();

        sActionLog::init('DELETE_PERMISSION');
        $d = $mPermission->where( 'id', $id )->delete();
        sActionLog::save( $d );
        return true;
    }

    /**
     * [check_permission_by_role_id 判断指定角色有无权限访问]
     * @param  integer $role_id     角色id
     * @param  string  $ctrler_name 控制器名
     * @param  string  $action_name 操作名
     * @return boolean              是否允许访问
     */
    public static function check_permission_by_user_id( $user_id, $ctrler_name, $action_name  ){
        $builder = mPermission::query_builder('p');
        $perrole = '\App\Models\PermissionRole';
        $user = '\App\Models\User';
        $userrole = '\App\Models\UserRole';

        $cond = array(
            'ur.uid = '. $user_id,
            'p.controller_name=\''.$ctrler_name.'\'',
            'p.action_name=\''.$action_name.'\''
        );
        $data = $builder->join($perrole, 'p.id = pr.permission_id', 'pr', 'LEFT')
                        ->join($userrole, 'pr.role_id = ur.role_id', 'ur', 'LEFT')
                        ->where( implode(' AND ', $cond) )
                        ->getQuery()
                        ->execute();

        $data = $data -> toArray();
        if(empty($data)){
            return false;
        }
        else{
            return true;
        }

    }
}
