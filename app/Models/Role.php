<?php
namespace App\Models;

class Role extends ModelBase
{

    protected $table = 'roles';

    public function get_role_by_uid($uid) {
        $role = self::where('uid', $uid)
            ->first();
    }

    public function get_role_by_id($id){
        $role = self::find($id);

        return $role;
    }

    public function get_roles( $roles = [] ) {
        $query = $this;
        if( $roles && is_array( $roles ) ){
            $query = $query->whereIn( 'id', $roles );
        }
        return $query->get();
    }

}
