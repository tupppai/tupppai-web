<?php
namespace App\Services;
use \App\Models\Role as mRole;

class Role extends ServiceBase
{

    public static function addNewRole ( $name, $display_name ) {
        $role = new mRole;
        $role->assign(array(
            'name'=>$name,
            'display_name'=>$display_name
        ));

        #todo: ActionLog
        $ret = $role->save();
        return $ret;
    }

    public static function updateRole ( $id, $name, $display_name ) {
        $role = (new mRole)->get_role_by_id($id);
        if (!$role) {
            return error('ROLE_NOT_EXIST');
        }

        $role->name = $name;
        $role->display_name = $display_name;

        #todo: ActionLog
        return $role->save();
    }

    public static function getRoleById ($id) {
        $role = (new mRole)->get_role_by_id($id);

        return $role;
    }

    public static function getRoleByUid ($uid) {
        $role = (new mRole)->get_role_by_uid($uid);

        return $role;
    }

    public static function getRoles() {
        return (new mRole)->get_roles();
    }
}
