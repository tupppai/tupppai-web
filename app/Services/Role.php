<?php
namespace Psgod\Services;
use \Psgod\Models\Role as mRole;

class Role extends ServiceBase
{

    public static function addNewRole ( $name, $display_name ) {
        $role = new mRole;
        $role->assign(array(
            'name'=>$name,
            'display_name'=>$display_name
        ));

        $ret = $role->save();
        return $ret;
    }

    public static function updateRole ( $id, $name, $display_name ) {
        $role = mRole::findFirst($id);
        if (!$role) {
            return error('ROLE_NOT_EXIST');
        }

        $role->name = $name;
        $role->display_name = $display_name;

        $ret = $role->save();
        return $ret;
    }

    public static function getRoleByUid ($uid) {
        $role = mRole::findFirst("uid={$uid}");

        return $role;
    }
}
