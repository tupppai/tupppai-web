<?php
namespace App\Services;
use \App\Models\Role as mRole;
use App\Services\ActionLog as sActionLog;

class Role extends ServiceBase
{

    public static function addNewRole ( $name, $display_name ) {
        $role = new mRole;
        $role->assign(array(
            'name'=>$name,
            'display_name'=>$display_name
        ));

        sActionLog::init('ADD_NEW_ROLE' );
        #todo: ActionLog
        $ret = $role->save();
        sActionLog::save( $ret );
        return $ret;
    }

    public static function updateRole ( $id, $name, $display_name ) {
        $role = (new mRole)->get_role_by_id($id);
        if (!$role) {
            return error('ROLE_NOT_EXIST');
        }
        sActionLog::init( 'UPDATE_ROLE', $role );

        $role->name = $name;
        $role->display_name = $display_name;

        #todo: ActionLog
        $r = $role->save();

        sActionLog::save( $r );
        return $r;
    }

    public static function getRoleById ($id) {
        $role = (new mRole)->get_role_by_id($id);

        return $role;
    }

    public static function getRoleByUid ($uid) {
        $role = (new mRole)->get_role_by_uid($uid);

        return $role;
    }

    public static function getRoles( $roles = [] ) {
        return (new mRole)->get_roles( $roles );
    }

    public static function detail( $role ){
        $arr = [];
        $arr['id'] = $role['id'];
        $arr['name'] = $role['name'];
        $arr['display_name'] = $role['display_name'];
        return $arr;
    }
}
