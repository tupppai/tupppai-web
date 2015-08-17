<?php
namespace App\Http\Controllers\Android;

use App\Services\User as sUser;
use App\Services\Follow as sFollow;
use App\Services\Download as sDownload;
use App\Services\UserDevice as sUserDevice;
use App\Models\UserDevice as mUserDevice;

class ProfileController extends ControllerBase{

    public function viewAction( ){
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $user   = sUser::getUserByUid( $uid );
        $user   = sUser::detail($user);

        return $this->output( $user );
    }

    public function fansAction(){
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );

        $fansList = sUser::getFans( $uid, $page, $size );

        return $this->output( $fansList );
    }

    public function friendsAction(){
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );

        $friendsList = sUser::getFriends( $this->_uid, $uid, $page, $size );
        $masterList = sUser::getMasterList( $this->_uid );

        return $this->output( ['fellows' => $friendsList, 'recommends' => $masterList ] );
    }

    public function updatePasswordAction(){
        $uid = $this->_uid;
        $oldPassword = $this->post( 'old_pwd', 'string' );
        $newPassword = $this->post( 'new_pwd', 'string' );

        if( $oldPassword == $newPassword ) {
            #todo: 不能偷懒，俺们要做多语言的  ←重点不是多语言，而是配置化提示语。方便后台人员直接修改。
            return error( 'WRONG_ARGUMENTS', '新密码不能与原密码相同' );
        }

        $ret = sUser::updatePassword( $uid, $oldPassword, $newPassword );

        return $this->output( $ret );
    }

    public function updateAction(){
        $uid = $this->_uid;

        $nickname = $this->post( 'nickname', 'string' );
        $avatar   = $this->post( 'avatar'  , 'string' );
        $sex      = $this->post( 'sex'     , 'integer');
        $location = $this->post( 'location', 'string' );
        $city     = $this->post( 'city'    , 'string' );
        $province = $this->post( 'province', 'string' );

        $ret = sUser::updateProfile(
            $uid,
            $nickname,
            $avatar,
            $sex,
            $location,
            $city,
            $province
        );

        return $this->output( $ret );
    }

    public function followAction(){
        $friendUid = $this->post( 'uid', 'integer' );
        $status = $this->post( 'status', 'integer', 1 );
        if( !$friendUid ){
            return error( 'WRONG_ARGUMENTS', '请选择关注的账号' );
        }

        $followResult = sFollow::follow( $this->_uid, $friendUid, $status );
        return $this->output( $followResult );
    }

    //UNDONE
    public function downloadedAction(){
        $uid = $this->_uid;
        $page = $this->get('page','int',1);
        $size = $this->get('size','int',10);
        $last_updated = $this->get('last_updated', 'int', time());

        $downloadedItems = sDownload::getDownloaded($uid, $last_updated, $page, $size);

        return $this->output( $downloadedItems );
    }

    public function get_push_settingsAction(){
        $uid = $this->_uid;
        if( empty( $uid ) ){
            return false;
        }

        $settings = sUserDevice::get_push_settings( $uid );

        return $this->output( $settings );
    }

    public function set_push_settingsAction(){
        $type = $this->post('type','string');
        $value = $this->post('value','string');

        $uid = $this->_uid;
        if( !in_array($type, array(
            mUserDevice::PUSH_TYPE_COMMENT,
            mUserDevice::PUSH_TYPE_FOLLOW,
            mUserDevice::PUSH_TYPE_INVITE,
            mUserDevice::PUSH_TYPE_REPLY,
            mUserDevice::PUSH_TYPE_SYSTEM))
        ){
            return error( 'WRONG_ARGUMENTS', '设置类型错误' );
        }
        if( $value!=mUserDevice::VALUE_ON && $value!=mUserDevice::VALUE_OFF ){
            return error( 'WRONG_ARGUMENTS', '设置参数错误' );
        }
        $ret = sUserDevice::set_push_setting( $uid, $type, $value );
        return $this->output( (bool)$ret );
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
