<?php
namespace App\Models;

class Permission extends ModelBase
{

    protected $table = 'permissions';
    protected $guarded = ['id', 'create_time', 'update_time'];

    //public static function save_permission($pid = null, $display_name, $controller_name, $action_name)
    //public static function check_exists($controller_name, $action_name){
    //public static function delete_permission($id){
    //public static function check_permission_by_user_id( $user_id, $ctrler_name, $action_name  ){
}
