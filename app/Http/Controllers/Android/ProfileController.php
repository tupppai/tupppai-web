<?php
namespace App\Http\Controllers\Android;

use App\Services\User as sUser;

class ProfileController extends ControllerBase{

    /**
     * 获取用户个人信息
     */
    public function infoAction(){
        $user = sUser::getUserByUid($this->_uid);
        $data = sUser::detail($user);

        return $this->output($data);
    }



    /**
     * [editAction 修改个人资料]
     * @return [type] [description]
     */
    public function editAction(){
        $uid = $this->_uid;

        $nickname = $this->post('nickname');
        $avatar   = $this->post('avatar');
        $sex      = $this->post('sex');
        $location = $this->post('location');
        $city     = $this->post('city');
        $province = $this->post('province');

        $user = sUser::getUserByUID($uid);
        if( !$user ){
            return $this->output( false, 'user doesn\'t exists' );
        }
        $old = sActionLog::init( 'MODIFY_USER_INFO', $user );

        if($nickname && $nickname != $user->nickname ) {
            if($a = sUser::getUserByNickname($nickname)) {
                $data = array('result' => 2);
                return $this->output( $data, 'nickname be used' );
            }
            $user->nickname = $nickname;
        }

        if($avatar) {
            $user->avatar = $avatar;
        }

        if($sex) {
            $user->sex = $sex;
        }

        if($location || $city || $province) {
            $location = $this->encode_location($province, $city, $location);
            $user->location = $location;
        }

        $user->update_time = time();

        // 保存数据
        if ( $user->save( ) ) {
            $data = array('result' => 1);
            sActionLog::save( $user );
            return $this->output( $data, 'ok' );
        }else{
            $data = array('result' => 0);
            return $this->output( $data, 'error' );
        }
    }



    /**
     * 我的作品Reply
     */
    public function my_replyAction() {
        $uid            = $this->_uid;
        $page           = $this->get("page", "int", 1);
        $size           = $this->get("size", "int", 15);
        $last_updated   = $this->get("last_updated", "int", time());

        //我的作品 Reply
        $reply_items    = sReply::userReplyList($uid, $last_updated, $page, $size);

        return $this->output( $reply_items );
    }

    /**
     * 我的求P
     */
    public function my_askAction() {
        $uid            = $this->_uid;
        $page           = $this->get("page", "int", 1);
        $size           = $this->get("size", "int", 15);
        $last_updated   = $this->get("last_updated", "int", time());

        //我的求P
        $ask_items      = sAsk::getUserAsks($uid, $last_updated, $page, $size);

        return $this->output( $ask_items );
    }

    /**
     * [my_collectionAction 我的收藏]
     * @return [type] [description]
     */
    public function my_collectionAction(){
        $uid          = $this->_uid;

        $page         = $this->get('page', 'int', 1);       // 页码
        $size         = $this->get('size', 'int', 15);   // 每页显示数量
        $width        = $this->get('width', 'int', 480);
        $last_updated = $this->post('last_updated', 'int', time());

        // 我的收藏
        $collected_items  = sReply::collectionList($uid, $page, $size);

        return $this->output( $collected_items, "okay" );
    }

    /**
     * [my_focusAction 我的关注]
     * @return [type] [description]
     */
    public function my_focusAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        // 我的关注
        $ask_items    = Ask::focusList($uid, $page, $size);
        $data = array();
        foreach ($ask_items as $ask) {
            $data[] = $ask->toStandardArray($uid, $width);
        }

        return $this->output( $data, "okay" );
    }

    /**
     * [my_focusAction 我的关注]
     * @return [type] [description]
     */
    public function my_collectionfocusAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $items = User::getCollectionFocus($this->_uid, $last_updated, $page, $width);
        $data  = array();
        foreach($items as $item) {
            if($item['type'] == Label::TYPE_ASK)
                $model = new Ask();
            else
                $model = new Reply();

            foreach($item as $key=>$val){
                $model->$key = $val;
            }
            $data[] = $model->toStandardArray($uid, $width);
        }

        return $this->output( $data, "okay" );
    }

    /**
     * 获取我的粉丝列表
     * @return [type] [description]
     */
    public function myFansAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $data = array();
        $data = User::myFansList($this->_uid, $page, $size);
        return $this->output( $data );
    }

    /**
     * 获取我的fellow列表
     * @return [type] [description]
     */
    public function myFellowAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $uid  = $this->get('uid', 'int', $this->_uid);

        $data = array();
        $recommends = array();
        $data = User::myFellowList($this->_uid, $page, $size);
        $recommends = User::recommendFellows($this->_uid);
        return $this->output( array(
            'recommends'=>$recommends,
            'fellows'=>$data
        ));
    }


    public function othersAction() {
        $uid  = $this->get('uid',  'int');
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $width= $this->get('width', 'int', 480);
        $type = $this->get('type', 'int', 0);
        $last_updated = $this->get('last_updated', 'int', time());
        if( !$uid ){
            return $this->output( '请选择用户' );
        }
        $user = User::findFirst($uid);
        if(!$user) {
            return $this->output( '请选择用户' );
        }

        $data = array();
        $data = $user->to_simple_array();
        $data['is_fans'] = $user->is_fans_to($this->_uid);
        $data['is_fellow'] = $user->is_fellow_to($this->_uid);

        $data['asks'] = array();
        if($page == 1  || $type == Label::TYPE_ASK) {
            $asks = Ask::userAskList($uid, $last_updated, $page, $size);
            foreach ($asks as $ask) {
                $data['asks'][] = $ask->toStandardArray($uid, $width);
            }
        }
        $data['replies'] = array();
        if($page == 1 || $type == Label::TYPE_REPLY) {
            $replies = Reply::userReplyList($uid, $last_updated, $page, $size);
            foreach ($replies as $reply) {
                $data['replies'][] = $reply->toStandardArray($uid, $width);
            }
        }
        return $this->output( $data );
    }

    public function othersFansAction(){
        $uid  = $this->get('uid',  'int', 3);
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $data = array();
        $data = User::othersFansList($uid, $this->_uid);

        return $this->output( $data );
    }

    public function othersFellowAction(){
        $uid  = $this->get('uid',  'int', 3);
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $data = array();
        $data = User::othersFellowList($this->_uid, $uid);
        return $this->output( $data );
    }

    public function my_proceedingAction() {

        $uid = $this->_uid;
        $page = $this->get('page','int',1);
        $size = $this->get('size','int',10);
        $width = $this->get('width', 'int', '480');
        $last_updated = $this->get('last_updated', 'int', time());

        $items = Download::get_progressing($uid, $last_updated, $page, $size)->items;
        $data = array();
        foreach ($items as $item) {
            if($item->type == Download::TYPE_ASK) {
                $ask = Ask::findFirst($item->target_id);
                $data[] = $ask->toStandardArray($uid, $width);
            } else {
                $reply = Reply::findFirst($item->target_id);
                $data[] = $reply->toStandardArray($uid, $width);
            }
        }

        return $this->output( $data );
    }

    public function followAction() {
        $uid = $this->post('uid');
        if(!$uid)
            return $this->output( '请选择关注的账号' );

        $me  = $this->_uid;

        $ret = Follow::setUserRelation($uid, $me, Follow::STATUS_NORMAL);
        if($ret){
            if( $ret instanceof Follow ){
                ActionLog::log(ActionLog::TYPE_FOLLOW_USER, array(), $ret);
            }
            return $this->output( 1 );
        }
        else
            return $this->output( 'error' );
    }

    public function unfollowAction() {
        $uid = $this->post('uid');
        $me  = $this->_uid;
        $ret = Follow::setUserRelation($uid, $me, Follow::STATUS_DELETED);

        if($ret){
            if( $ret instanceof Follow ){
                ActionLog::log(ActionLog::TYPE_UNFOLLOW_USER, array(), $ret);
            }
            return $this->output( 1 );
        }
        else
            return $this->output( 'error' );
    }


    //通过原密码修改密码
    public function chg_passwordAction(){
        $old_pwd = $this->post('old_pwd');
        $new_pwd = $this->post('new_pwd');
        $uid = $this->_uid;

        if( $old_pwd == $new_pwd ) {
            return $this->output( 3, '新密码不能与原密码相同' );
        }
        $user = User::findFirst($uid);
        if( !$user ){
            return $this->output( false, 'user not exist' );
        }

        $old = ActionLog::clone_obj( $user );
        if( !User::verify( $old_pwd, $user->password ) ){
            return $this->output( 2, '原密码校验失败' );
        }

        $user = User::set_password( $uid, $new_pwd );
        //坑！$user instanceof User 居然是flase！因为$user 是Android\User
        if( $user ){
            ActionLog::log(ActionLog::TYPE_CHANGE_PASSWORD, $old, $user);
            return $this->output( true  );
        }
        else{
            return $this->output( false , 'error' );
        }

    }





    /**
     * [recordAction 记录下载]
     * @param type 求助or回复
     * @param target 目标id
     * @return [json]
     */
    public function recordAction() {
        $type       = $this->get('type');
        $target_id  = $this->get('target');
        $width      = $this->get('width', 'int', 480);
        $uid = $this->_uid;

        $url = '';
        if($type=='ask') {
            $type = Download::TYPE_ASK;
            if($ask = Ask::findFirst($target_id)) {
                $image  = $ask->upload->resize($width);
                $url    = $image['image_url'];
            }
        }
        else if($type=='reply') {
            $type = Download::TYPE_REPLY;
            if($reply = Reply::findFirst($target_id)) {
                $image  = $reply->upload->resize($width);
                $url    = $image['image_url'];
            }
        }
        else{
            return $this->output( '未定义类型。' );
        }

        if($url==''){
            return $this->output( '访问出错' );
        }

        //$ext = substr($url, strrpos($url, '.'));
        //todo: watermark
        //$url = watermark2($url, '来自PSGOD', '宋体', '1000', 'white');
        //echo $uid.":".$type.":".$target_id.":".$url;exit();

        //$d = Download::has_downloaded($type, $uid, $target_id);
        if($d = Download::has_downloaded($uid, $type, $target_id)){
            $d->url = $url;
            $d->save_and_return($d);
        } else {
            $dl = Download::addNewDownload($uid, $type, $target_id, $url, 0);
            if( $dl instanceof Download ){
                ActionLog::log(ActionLog::TYPE_USER_DOWNLOAD, array(), $dl);
            }
        }

        return $this->output( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }

    public function delete_progressAction() {
        $type = $this->post("type", "int", Label::TYPE_ASK);
        $id   = $this->post("id", "int");

        if(!$id){
            return $this->output( false, '请选择删除的记录' );
        }

        $uid = $this->_uid;
        $download = Download::findFirst('uid='.$uid.' AND type='.$type.' AND target_id='.$id);
        if(!$download){
            return $this->output( false, '请选择删除的记录' );
        }

        if($download->uid != $this->_uid){
            return $this->output( false, '未下载' );
        }
        $old = ActionLog::clone_obj( $download );

        $download->status = Download::STATUS_DELETED;
        $new = $download->save_and_return($download);
        if( $new instanceof Download ){
            ActionLog::log(ActionLog::TYPE_DELETE_DOWNLOAD, $old, $new);
        }

        return $this->output( true );
    }



    public function get_push_settingsAction(){
        $type = $this->get('type','string','');

        $uid = $this->_uid;
        $settings = UserDevice::get_push_stgs( $uid );

        switch( $type ){
            case UserDevice::PUSH_TYPE_COMMENT:
            case UserDevice::PUSH_TYPE_FOLLOW:
            case UserDevice::PUSH_TYPE_INVITE:
            case UserDevice::PUSH_TYPE_REPLY:
                $ret = array($type=>$settings->$type);
                break;
            default:
                $ret = $settings;
        }

        return $this->output( $ret );
    }

    public function set_push_settingsAction(){
        $this->noview();
        $type = $this->post('type','string');
        $value = $this->post('value','string');

        $uid = $this->_uid;
        if( !in_array($type, array(
            UserDevice::PUSH_TYPE_COMMENT,
            UserDevice::PUSH_TYPE_FOLLOW,
            UserDevice::PUSH_TYPE_INVITE,
            UserDevice::PUSH_TYPE_REPLY,
            UserDevice::PUSH_TYPE_SYSTEM))
        ){
            return $this->output( false, '设置类型错误' );
        }
        if( $value!=UserDevice::VALUE_ON && $value!=UserDevice::VALUE_OFF ){
            return $this->output( false, '设置参数错误' );
        }

        $settings = UserDevice::get_push_stgs( $uid );
        $old = ActionLog::clone_obj( $settings );
        switch( $type ){
            case UserDevice::PUSH_TYPE_COMMENT:
            case UserDevice::PUSH_TYPE_FOLLOW:
            case UserDevice::PUSH_TYPE_INVITE:
            case UserDevice::PUSH_TYPE_REPLY:
            case UserDevice::PUSH_TYPE_SYSTEM:
                $ret = UserDevice::set_push_stgs( $uid, $type, $value );
                ActionLog::log(ActionLog::TYPE_USER_MODIFY_PUSH_SETTING, $old, $ret);
                break;
            default:
                $ret = false;
        }

        return $this->output( (bool)$ret );
    }


    public function get_recommend_usersAction(){
        $recom_user = array();
        $recom_user['recommends'] = Master::get_master_list(1,2);
        $recom_user['fellows'] = User::myFellowList($this->_uid);
        return $this->output( $recom_user );
    }

    public function get_mastersAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        return $this->output( Master::get_master_list($page,$size) );
    }
}
