<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\ActionLog as sActionLog,
    App\Services\Device as sDevice,
    App\Services\Ask as sAsk,
    App\Services\Comment as sComment,
    App\Services\Inivitation as sInivitation,
    App\Services\SysMsg as sSysMsg,
    App\Services\Reply as sReply,
    App\Services\Follow as sFollow,
    App\Services\User as sUser,
    App\Services\Focus as sFocus,
    App\Services\Upload as sUpload,
    App\Services\Download as sDownload,
    App\Services\Collection as sCollection,
    App\Services\UserLanding as sUserLanding,
    App\Services\UserDevice as sUserDevice;

use App\Models\User as mUser,
    App\Models\UserLanding as mUserLanding,
    App\Models\Download as mDownload;
use App\Facades\CloudCDN;
use App\Jobs\Push, Queue;

use App\Models\Message as mMessage;

class UserController extends ControllerBase
{
    public $_allow = array(
        'login',
        'get_mobile_code',
        'save',
        'device_token',
        'check_token',
        'check_mobile',
        'test'
    );

    public function __construct(){
        parent::__construct();
    }

    public function viewAction() {
        $user = sUser::getUserByUid($this->_uid);

        return $this->output(sUser::detail($user));
    }

    public function testAction(){

    }

    public function searchAction() {
        $page  = $this->post('page', 'int', 1);           // 页码
        $size  = $this->post('size', 'int', 15);       // 每页显示数量
        $name  = $this->post('name', 'string');

        $users = sUser::searchUserByName($name, $page, $size);

        return $this->output($users);
    }

    /**
     * [recordAction 记录下载]
     */
    public function recordAction() {
        $type       = $this->get('type');
        $target_id  = $this->get('target');
        $width      = $this->get('width', 'int', 480);
        $uid        = $this->_uid;

        if( !in_array($type, array('ask', 'reply') )){
            return error('WRONG_ARGUMENTS');
        }
        
        $url = '';
        if($type=='ask') {
            $model  = sAsk::getAskById($target_id);
            $type   = mDownload::TYPE_ASK;
        }
        else if($type=='reply') {
            $model  = sAsk::getAskById($target_id);
            $type   = mDownload::TYPE_ASK; 
        }

        if( !$model ) {
            return error('UPLOAD_NOT_EXIST');
        }

        $upload     = sUpload::getUploadById($model->upload_id);
        if( !$upload ){
            return error('UPLOAD_NOT_EXIST');
        }
        $url        = CloudCDN::file_url($upload->savename);

        if( !sDownload::hasDownloaded($uid, $type, $target_id) ){
            $dl = sDownload::addNewDownload($uid, $type, $target_id, $url, 0);
        }

        return $this->output( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }

    public function blockAction() {
        $uid = $this->post('uid', 'int');
        $status = $this->post('status', 'int', mUser::STATUS_BLOCKED);
        sFollow::blockUser($this->_uid, $uid, $status);

        return $this->output();
    }
}
