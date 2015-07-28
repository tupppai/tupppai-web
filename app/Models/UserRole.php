<?php
namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class UserRole extends ModelBase
{
    const ROLE_HELP = 1;
    const ROLE_WORK = 2;
    const ROLE_PARTTIME = 3;
    const ROLE_STAFF    = 4;

    public function getSource()
    {
        return 'user_roles';
    }

    //public static function check_authentication($uid, $role_id){
    //public static function get_role_users($role_id){
    //public static function assign_role( $user_id, $role_ids ){
    //public static function get_roles_by_user_id( $uid ){
}
