<?php namespace App\Http\Middleware;

use Closure;
use App\Services\User as sUser,
    App\Services\UserRole as sUserRole,
    App\Services\UserScheduling as sUserScheduling;

use App\Models\UserRole as mUserRole;

class AdminAuthMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        $this->_uid = session('uid');
        // for ios test
        if(env('APP_DEBUG') && !$this->_uid){
            $this->_uid = 1;
            session(['uid', 1]);
        }
        else if(controller() == 'login') {
            return $next($request);
        }
        else if(!$this->_uid ) {
            return redirect('login');
        }
        $this->user = sUser::getUserInfoByUid($this->_uid);
        session(['user'=>$this->user]); 

        //if (!in_array($this->_uid, array(mUserRole::SUPER_USER_UID , mUserRole::STAFF_USER_UID))
        if (!in_array($this->_uid, array(1, 2, 3, 4, 5,  655))
            && !sUserRole::checkAuth($this->_uid, mUserRole::ROLE_STAFF) ) {
            return redirect('login');
        }
        if (!$this->_uid || !$this->user ){
            return redirect('login');
        }
        
        if( !$this->check_work_time() ){
            #todo: redis 15min not operate kit out
            return redirect('login');
        }
        return $next($request);
    }

    /**
     * [check_permission 检测模块操作权限]
     * @return [type] [description]
     */
    private function check_permission(){
        if( CHECK_PERMISSIONS == FALSE ){
            return true;
        }

        // 超级管理员默认拥有所有模块访问权限
        if ($this->_uid == mUserRole::SUPER_USER_UID) return true;

        //$permissions
        $controller_name = controller();
        $action_name     = action();

        $permissions = array(
            array(          // 默认拥有首页访问权限
                'controller_name' => 'index',
                'action_name'     => 'index'
            )
        );

        return false;
    }

    private function check_work_time (){
        // 超级管理员不需要检测权限
        if ($this->_uid == mUserRole::SUPER_USER_UID) return true;

        if(!sUserScheduling::checkScheduling($this->user)){
            return false;
        }
        return true;
    }
}
