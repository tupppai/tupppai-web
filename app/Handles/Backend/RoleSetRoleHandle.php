<?php
/**
 * Created by PhpStorm.
 * User: zhiyong
 * Date: 16/1/12
 * Time: 下午2:00
 */

namespace App\Handles\Backend;


use App\Events\Event;
use App\Services\Role;

class RoleSetRoleHandle
{
    public function handle(Event  $event)
    {
        list($role_id, $role_name, $role_display_name) = $event->arguments;
        if( $role_id ){
            $role = Role::getRoleById($role_id);
            $newRole = Role::updateRole($role_id, $role_name, $role_display_name);
        }
        else {
            $role = Role::addNewRole ( $role_name, $role_display_name );
        }
    }
}