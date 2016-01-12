<?php namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\Usermeta,
    App\Models\Role as mRole,
    App\Models\ActionLog,
    App\Models\Permission,
    App\Models\PermissionRole,
    App\Models\UserRole;

use App\Services\Role as sRole,
    App\Services\UserRole as sUserRole,
    App\Services\Permission as sPermission,
    App\Services\PermissionRole as sPermissionRole;

class RoleController extends ControllerBase{

    public function indexAction(){
        return $this->output();
    }

    public function list_rolesAction()
    {
        $role = new mRole;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("role_id", "int");
        $cond['role_name']           = array(
            $this->post("role_name", "string"),
            'LIKE'
        );
        $cond['display_name']   = array(
            $this->post("role_display_name", "string"),
            'LIKE'
        );
        $cond['create_time']        = $this->post("role_created", "string");
        $cond['update_time']        = $this->post("role_updated", "string");

        // 用于遍历修改数据
        $data  = $this->page($role, $cond);

        foreach($data['data'] as $row){
            $role_id = $row->id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $row->oper = "<a href='#set_previlege' data-toggle='modal' data-id='$role_id' class='set_previlege'>权限设置</a> <a href='#edit_role' data-toggle='modal' data-id='$role_id' class='edit'>编辑</a>";
        }
        // 输出json
        return $this->output_table($data);
	}

    public function set_roleAction(){
        $role_id    = $this->post("role_id", "int");
        $role_name  = $this->post("role_name", "string");
        $role_display_name = $this->post("role_display_name", "string");

        if(is_null($role_name) || is_null($role_name)){
            return error('EMPTY_NAME');
        }

        fire('BACKEND_HANDLE_ROLE_SETROLE',[
            $role_id,
            $role_name,
            $role_display_name
        ]);

        return $this->output();
    }

    /**
     * [user_permission 用户权限]
     * @return [type] [description]
     */
    public function user_permission(){
        if ($this->request->ajax()) {

        }
    }

    /**
     * [permissionAction 权限列表]
     * @return [type] [description]
     * @author [Vanson] <[Y-m-d H:i:s]>
     */
    public function list_permissionsAction(){
        $request = $this->request;
        if ($request::ajax()) {
            $permission = new Permission;
            // 检索条件
            $cond = array();
            $cond['id']             = $this->post("pid", "int");
            $cond['controller_name'] = array(
                $this->post("controller_name", "string"),
                'LIKE'
            );

            $cond['action_name']     = array(
                $this->post("action_name", "string"),
                'LIKE'
            );

            $cond['display_name']    = array(
                $this->post("display_name", "string"),
                'LIKE'
            );

            // 用于遍历修改数据
            $data = $this->page($permission, $cond);

            foreach($data['data'] as $row){
                $row->create_time = date('Y-m-d H:i:s', $row->create_time);
                $row->update_time = date('Y-m-d H:i:s', $row->update_time);
                $row->oper = "<a href='#edit_permission' data-toggle='modal' data='" . $row->id . "' class='edit'>编辑</a> <a href='#delete_permission' data-toggle='modal' title='" . $row->display_name . "' data='" . $row->id . "' class='delete'>删除</a>";
            }

            // 输出json
            return $this->output_table($data);
        }

        return $this->output();
    }

    /**
     * [edit_permissionAction 保存权限模块]
     */
    public function save_permissionAction(){

        $pid             = $this->post("pid", 'int', null);
        $display_name    = $this->post("display_name", "string");
        $controller_name = $this->post("controller_name", "string");
        $action_name     = $this->post("action_name", "string");

        if( !$display_name ){
            return error( 'EMPTY_DISPLAY_NAME', '请输入权限名');
        }
        if( !$controller_name ){
            return error( 'EMPTY_CONTROLLER_NAME', '请输入控制器名' );
        }
        if( !$action_name ){
            return error( 'EMPTY_ACTION_NAME', '请输入操作名');
        }

        if( $pid ){
            $updatePermission = sPermission::updatePermission( $pid, $display_name, $controller_name, $action_name);
        }
        else{
            $updatePermission = sPermission::addNewPermission( $display_name, $controller_name, $action_name );
        }

        return $this->output_json(['result'=>'ok','permission'=>$updatePermission]);
    }

    /**
     * [delete_permissionAction 删除权限]
     * @return [type] [description]
     */
    public function delete_permissionAction(){
        $id = $this->post("pid", 'int');

        sPermission::deletePermission( $id );

        return $this->output_json(['result'=>'ok']);
    }

    /**
     * [get_permissions_by_role_id 获取角色对应所拥有的权限]
     * @return  json [返回拥有的权限id，以逗号分隔的字符串]
     */
    public function get_permissions_by_role_idAction(){
        $request = $this->request;
        if (!$request::ajax()) {
            //return error('SYSTEM_ERROR');
        }

        $role_id= $this->post('role_id','int');
        if( !$role_id ){
            return error('EMPTY_ROLE_ID');
        }

        $permissions = sPermissionRole::getPermissionsByRoleId($role_id);
        if(empty($permissions)){
            $permissions = '';
        }
        return $this->output($permissions);
    }

    /**
     * [save_privilege 赋予某个角色权限]
     * @return  boolean [是否保存成功]
     */
    public function save_previlegeAction(){
        $role_id = $this->post('role_id','int');
        $permission_ids = $this->post('permission_id','int');

        if( !$role_id ){
            return error('EMPTY_ROLE_ID');
        }

        $ret = sPermissionRole::updatePermissions( $role_id, $permission_ids );
        return $this->output_json(['result' => $ret ]);
    }


    /**
     * [get_roles_by_user_idAction 获取用户拥有的角色]
     * @return [string] [角色id，以逗号分隔的字符串]
     */
    public function get_roles_by_user_idAction(){
        $user_id= $this->post('user_id','int');
        if( !$user_id ){
            return error('EMPTY_UID','请选择用户');
        }

        $permissions = sUserRole::getRoleStrByUid( $user_id );
        // $mUserRole = new mUserRole();
        // $permissions = $mUserRole->get_user_roles_by_uid($user_id);
        // if(empty($permissions)){
        //     $permissions = '';
        // }
        return $this->output_json( [ 'roles' => $permissions ] );
    }

    /**
     * [assign_roleAction 赋予用户权限]
     * @return [boolean] [是否赋予成功]
     */
    public function assign_roleAction(){
        $user_id = $this->post('user_id','int');
        $role_ids = $this->post('role_id','int');

        if( empty($user_id) ){
            return error( 'EMPTY_UID', '没有角色id' );
        }

        if( empty($role_ids) ){
            return error('EMPTY_ROLE_ID');
        }

        $role = sUserRole::assignRole( $user_id, $role_ids );
        return $this->output( ['result'=>'ok'] );
    }

	public function get_rolesAction(){
		$type = $this->post('type', 'string', '');
		$role_ids = [];
		if( $type == 'puppet' ){
			$role_ids = [
				mRole::TYPE_HELP,
				mRole::TYPE_WORK,
				mRole::TYPE_CRITIC
			];
		}

		$r = sRole::getRoles( $role_ids );
		$roles = [];
		foreach( $r as $role ){
			$roles[] = sRole::detail( $role );
		}

		return $this->output_json( $roles );
	}
}
