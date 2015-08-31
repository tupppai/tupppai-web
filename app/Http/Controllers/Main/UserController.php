<?php 
namespace App\Http\Controllers\Main; 

class UserController extends ControllerBase {
    public $_allow = array('home');

    /**
     * 用户个人页面
     * @params $uid
     * @author brandwang
     */   
    public function homeAction($uid) {
        return $this->output();
    } 

    /**
     * 排行榜页面
     *
     * @author brandwang
     */
    public function rankingAction() {
        return $this->output();
    }
}
?>
