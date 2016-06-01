<?php namespace App\Http\Controllers\Admin;

use App\Facades\EasyWeChat;
use App\Http\Controllers\Controller;
use EasyWeChat\Foundation\Application;

use App\Services\Config as sConfig;
use App\Services\ActionLog as sActionLog;

use Log;

class WXMPManageController extends Controller {
    public function indexAction(){
        echo '<a href="/WXMPManage/update_menu">更新菜单</a>';
    }

    public function usersAction(){
        $easyWX = EasyWeChat::getFacadeRoot();
        $openids = $easyWX->user->lists();
        $users = [];
        foreach( $openids['data']['openid'] as $openid ){
            $users[] = $easyWX->user->get($openid)->toArray();
        }
        dd($users);
    }

    public function update_menuAction(){
        $easyWX = EasyWeChat::getFacadeRoot();

        $wxid = $this->post('APPID', 'string');
        $wxid = 'wxa0b2dda705508552';

        //暂时不做个性化菜单
        $oldMenu = $easyWX->menu->all();
        $oldMenu = $oldMenu->toArray();
        if(isset($oldMenu['menu'])){
            if( isset($oldMenu['menu']['button'])){
                $oldMenu = $oldMenu['menu'];
            }
            else{
                $oldMenu = [];
            }
        }
        else{
            $oldMenu = [];
        }

        sActionLog::init('UPDATE_WX_MENU', $oldMenu);
        //get new menu
        $menus = config('wechatmenu');
        if( !isset( $menus[$wxid] ) ){
            return error( 'WXMENU_CONFIG_NOT_EXIST', $wxid.'的微信菜单配置不存在');
        }
        $newMenu = $menus[$wxid];
        $setResult = $easyWX->menu->add($newMenu)->toArray();
        sActionLog::save($newMenu);
        if( $setResult['errcode'] == 0 ){
            echo 'done!';
        }
    }
}
