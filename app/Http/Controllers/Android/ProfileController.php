<?php
namespace App\Http\Controllers\Android;

use App\Services\User as sUser;
use App\Services\Follow as sFollow;
use App\Services\Download as sDownload;
use App\Services\Reply as sReply;
use App\Services\Focus as sFocus;
use App\Services\Ask as sAsk;
use App\Services\Master as sMaster;
use App\Services\UserDevice as sUserDevice;

use App\Models\UserDevice as mUserDevice;
use App\Models\Download as mDownload;

class ProfileController extends ControllerBase{

    public function viewAction( ){
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'integer', 1);
        $size   = $this->get( 'size', 'integer', 15);
        $type   = $this->get( 'type', 'integer');

        $user   = sUser::getUserByUid( $uid );
        $user   = sUser::detail($user);
        $user   = sUser::addRelation( $this->_uid, $user );

        //todo: remove asks & replies 
        if($page == 1  || $type == mDownload::TYPE_ASK) {
            $user['asks'] = sAsk::getUserAsks( $uid, $page, $size, time());
        }
        if($page == 1  || $type == mDownload::TYPE_REPLY) {
            $user['replies'] = sReply::getUserReplies( $uid, $page, $size, time() );
        }
        return $this->output( $user );
    }

    public function asksAction() {
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'integer', 1);
        $size   = $this->get( 'size', 'integer', 15);
        $lpd    = $this->get( 'last_updated', 'integer', time());

        $asks   = sAsk::getUserAsks( $uid, $page, $size, $lpd);
        return $this->output( $asks );
    }

    public function repliesAction() {
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'integer', 1);
        $size   = $this->get( 'size', 'integer', 15);
        $lpd    = $this->get( 'last_updated', 'integer', time());

        $replies= sReply::getUserReplies( $uid, $page, $size, $lpd );
        return $this->output( $replies );
    }

    public function fansAction(){
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );
        $lpd    = $this->get( 'last_updated', 'integer', time());

        $fansList = sUser::getFans( $uid, $page, $size, $lpd );

        return $this->output( $fansList );
    }

    public function followsAction(){
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );
        $ask_id = $this->get( 'ask_id', 'interger');
        $lpd    = $this->get( 'last_updated', 'integer', time());


        $friendsList = sUser::getFriends( $this->_uid, $uid, $page, $size, $ask_id, $lpd );
        $masterList = sMaster::getAvailableMasters( $this->_uid, 1, 2, $ask_id );
        $masterAmount = sMaster::countMasters();

        return $this->output( ['fellows' => $friendsList, 'recommends' => $masterList, 'totalMasters'=>$masterAmount ] );
    }

    public function updatePasswordAction(){
        $uid = $this->_uid;
        $oldPassword = $this->post( 'old_pwd', 'string' );
        $newPassword = $this->post( 'new_pwd', 'string' );

        if( empty( $oldPassword ) ){
            //return error( 'OLD_PASSWORD_EMPTY', '原密码不能为空' );
            return $this->output(0 , '原密码不能为空');
        }
        if( empty( $newPassword ) ){
            //return error( 'NEW_PASSWORD_EMPTY', '新密码不能为空' );
            return $this->output(0, '新密码不能为空');
        }
        if( $oldPassword == $newPassword ) {
            #todo: 不能偷懒，俺们要做多语言的  ←重点不是多语言，而是配置化提示语。方便后台人员直接修改。
            //return error( 'WRONG_ARGUMENTS', '新密码不能与原密码相同' );
            return $this->output(3, '新密码不能与原密码相同');
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

        $data = array( $uid, $nickname, $avatar, $sex, $location, $city, $province );
        if( count(array_filter( $data )) == 0 ){
            $ret = false;//Nothing changed.
        }
        else{
            $ret = sUser::updateProfile(
                $uid,
                $nickname,
                $avatar,
                $sex,
                $location,
                $city,
                $province
            );
        }

        return $this->output( ['result'=>(int)$ret] );
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



    public function get_mastersAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        return $this->output( sMaster::getAvailableMasters($this->_uid, $page,$size) );
    }

    public function downloadedAction(){
        $uid = $this->_uid;
        $page = $this->get('page','int',1);
        $size = $this->get('size','int',10);
        $last_updated = $this->get('last_updated', 'int', time());

        $downloadedItems = sDownload::getDownloaded($uid, $page, $size, $last_updated);

        return $this->output( $downloadedItems );
    }

    public function doneAction(){
        $uid = $this->_uid;
        $page = $this->get('page','int',1);
        $size = $this->get('size','int',10);
        $last_updated = $this->get('last_updated', 'int', time());

        $doneItems = sDownload::getDone($uid, $page, $size, $last_updated);

        return $this->output( $doneItems );
    }
    public function deleteDownloadRecordAction() {
        $uid = $this->_uid;
        $id   = $this->post("id", "int");

        if(!$id){
            return error( 'WRONG_ARGUMENTS', '请选择删除的记录' );
        }

        $uid = $this->_uid;
        $dlRecord = sDownload::deleteDLRecord( $uid, $id );

        return $this->output( $dlRecord );
    }

    /**
     * [收藏]
     * @return [type] [description]
     */
    public function collectionsAction(){
        $uid = $this->_uid;

        $page         = $this->get('page', 'int', 1);    // 页码
        $size         = $this->get('size', 'int', 15);   // 每页显示数量
        $width        = $this->get('width', 'int', 480);
        $last_updated = $this->post('last_updated', 'int', time());

        // 我的收藏
        $collected_items  = sReply::getCollectionReplies($uid, $page, $size);

        return $this->output( $collected_items );
    }

    /**
     * [我的关注]
     * @return [type] [description]
     */
    public function focusAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        // 关注
        $ask_items    = sFocus::getFocusByUid($uid, $page, $size);

        return $this->output( $ask_items );
    }

    /**
     * [recordAction 记录下载]
     * @param type 求助or回复
     * @param target 目标id
     * @return [json]
     */
    public function downloadFileAction() {
        $type       = $this->get('type', 'string');
        $target_id  = $this->get('target', 'string');
        $width      = $this->get('width', 'int', 480);
        $uid = $this->_uid;

        if( $type == 'ask' ){
            $type =mDownload::TYPE_ASK;
        }
        else if( $type == 'reply' ){
            $type =mDownload::TYPE_REPLY;
        }
        else{
            return error( 'WRONG_ARGUMENTS', '未定义类型' );
        }


        $url = sDownload::getFile( $type, $target_id );

        //$ext = substr($url, strrpos($url, '.'));
        //todo: watermark
        //$url = watermark2($url, '来自PSGOD', '宋体', '1000', 'white');
        //echo $uid.":".$type.":".$target_id.":".$url;exit();

        sDownload::saveDownloadRecord( $uid, $type, $target_id, $url );


        return $this->output( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }

}
