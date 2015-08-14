<?php
namespace App\Models;

class Role extends ModelBase
{
    const TYPE_HELP     = 1;
    const TYPE_WORK     = 2;
    const TYPE_PARTTIME = 3;
    const TYPE_STAFF    = 4;
    const TYPE_JUNIOR   = 5;

    protected $table = 'asks';

    public function get_role_by_uid($uid) {
        $role = self::where('uid', $uid)
            ->first();
    }

    public function get_role_by_id($id){
        $role = self::find($id);
    }
}
