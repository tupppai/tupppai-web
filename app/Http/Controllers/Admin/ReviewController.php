<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Usermeta;
use App\Models\Role;
use App\Models\Upload;
use App\Models\ActionLog;

use App\Facades\CloudCDN;

use App\Models\Review as mReview,
    App\Models\User as mUser;

use App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\Review as sReview,
    App\Services\Puppet as sPuppet,
    App\Services\User as sUser;
use Html, Form;

class ReviewController extends ControllerBase
{
    public $type    = null;
    public $status  = null;

    public function initialize()
    {
        parent::initialize();

        $users = sUserRole::getUsersByIds(array(
            UserRole::ROLE_WORK,
            UserRole::ROLE_HELP
        ));
        $work_uids  = array();
        $help_uids  = array();
        foreach($users as $user){
            if($user->role_id == UserRole::ROLE_WORK){
                $work_uids[] = $user->uid;
            }
            else {
                $help_uids[] = $user->uid;
            }
        }

        $this->type     = $this->get('type', 'int');
        $this->status   = $this->get('status', 'int', -5);

        view()->share('status', $this->status);
        view()->share('type', $this->type);
        view()->share('helps', $help_uids);
        view()->share('works', $work_uids);
        view()->share('users', $users);
    }

    public function askAction() {
        return $this->output();
    }

    public function replyAction() {
        return $this->output();
    }

    public function indexAction()
    {
        return $this->output();
    }

    public function batchAction()
    {
        return $this->output();
    }

    /**
     * 列举需要审核的批量发布
     */
    public function list_reviewsAction()
    {
        $cond = array();

        $uid = $this->post('uid','int');
        $username = $this->post('username', 'string');
        $nickname = $this->post('nickname', 'string');
        $status = $this->post('status', 'string');

        $review = new mReview;
        $user   = new mUser;
        // 检索条件
        //作品上传页面需要将已经发出去的求助拉出来
        if($this->type == mReview::TYPE_REPLY && $this->status == mReview::STATUS_HIDDEN) {
            $this->type     = mReview::TYPE_ASK;
            $this->status   = 1;
        }
        $cond[$review->getTable().'.type']    = $this->type;
        $cond[$review->getTable().'.status']  = $this->status;

        if( $username ){
            $cond[$ser->getTable().'.username'] = array(
                $username,
                "LIKE",
                "AND"
            );
        }

        if( $nickname ){
            $cond[$user->getTable().'.nickname'] = array(
                $nickname,
                "LIKE",
                "AND"
            );
        }

        $join = array();
        $join['Upload'] = array(
            'upload_id', 'id'
        );

        $join['User'] = array( 'uid', 'uid' );
        if( $status == 1){
            $orderBy = array($review->getTable().'.release_time desc');
        }
        else{
            $orderBy = array($review->getTable().'.release_time asc');
        }

        // 用于遍历修改数据
        $data = $this->page($review, $cond, $join, $orderBy);

        $arr  = array();

        $puppet_arr = array();
        $puppets = sPuppet::getPuppets($this->_uid, []);
        foreach($puppets as $puppet) {
            $puppet_arr[$puppet['uid']] = $puppet['username'];
        }

        foreach($data['data'] as $key => $row){
            $row_id = $row->id;
            $row->image_url = CloudCDN::file_url($row->savename);
            $row->image_view= Html::image($row->image_url, 'image_view', array('width'=>50));
            $row->avatar    = Html::image($row->avatar, 'avatar', array('width'=>50));
            $row->desc      = $row->labels;

            $row->checkbox  = Form::input('checkbox', 'checkbox', 0, array(
                'class' => 'form-control'
            ));
            $row->create_time = date('Y-m-d H:i', $row->create_time);

            $row->puppet_uid    = Form::select('puppet_uid',  $puppet_arr, $row->puppet_uid);
            $row->upload_id     = Form::input('file', 'upload_id');
            $row->upload_view = '<div>
                                <img width=50 class="user-portrait" src=" ">
                                <input id="upload_'.$row_id.'" type="file" class="form-control" style="left: 85px;top: 7px;">
                                <input name="upload_id" class="hide">
                                </div>';
            $row->puppet_desc   = Form::input('text', 'desc', '', array(
                'class' => 'form-control'
            ));
            $row->execute_time = date( 'Y-m-d H:i', $row->release_time);
            $row->release_time  = Form::input('text', 'release_time', date('Y-m-d H:i:s',$row->release_time), array(
                'class' => 'form-control',
                'style' => 'width: 140px; display: inline-block'
            ));

        }
        return $this->output_table($data);
    }

    public function set_statusAction(){

        $review_ids = $this->post("review_ids",'string');
        $status     = $this->post("status", "int", NULL);
        $data       = $this->post("data", "string", 0);

        if( !$review_ids ){
            return error( 'EMPTY_ID' );
        }

        if( is_null($status) ){
            return error( 'EMPTY_STATUS' );
        }
        $r = sReview::updateStatus( $review_ids, $status, $data );

        return $this->output_json( ['result'=>'ok'] );
    }

    public function set_batch_askAction(){

    }

    protected function _upload_error(){
        if(empty($_FILES)){
            return "请选择上传文件";
        }
        switch($_FILES['file']['error']) {
            case 1:
                return "文件大小超出了服务器的空间大小";
            case 2:
                return "要上传的文件大小超出浏览器限制";
            case 3:
                return "文件仅部分被上传";
            case 4:
                return "没有找到要上传的文件";
            case 5:
                return "服务器临时文件夹丢失";
            case 6:
                return "文件写入到临时文件夹出错";
            default:
                return "";
        }
    }

    /**
     * 批量上传文件，文件格式zip，文件名即求助内容
     */
    public function uploadAction()
    {
        if ($_FILES["file"]["error"] > 0) {
            echo $this->_upload_error();
            pr($_FILES["file"]["error"]);
            //return ajax_return(0, $_FILES["file"]["error"]);
        }
        /*
        if(!env('dev')) {
            $type = $_FILES["file"]["type"];
            if($type != "application/octet-stream" and $type != "application/zip"){
                pr("zip only");
            }
        }
         */
        $tmp = storage_path('zips/');
        if (!file_exists($tmp)) {
            mkdir($tmp, 0777, true);
        }

        $file_path = $tmp.md5(time().$_FILES["file"]["name"]).".zip";
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);

        $uploads = array();
        $zip = zip_open($file_path);
        if ($zip)
        {
            while ($zip_entry = zip_read($zip))
            {
                if (zip_entry_open($zip, $zip_entry))
                {
                    $file_name  = zip_entry_name($zip_entry);
                    $encode     = mb_detect_encoding($file_name, "auto");
                    if($encode != 'UTF-8') {
                        $file_name = iconv('gbk', 'UTF-8', $file_name);
                    }
                    $contents = "";
                    while($row = zip_entry_read($zip_entry)){
                        $contents .= $row;
                    }
                    //get file name
                    if($contents == "" || sizeof(explode(".", $file_name)) == 1){
                        continue;
                    }
                    $savename  = CloudCDN::generate_filename_by_file($file_name);

                    $upload_dir = env('IMAGE_UPLOAD_DIR');
                    $upload_dir = $upload_dir . date("Ym")."/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $path = $upload_dir.$savename;
                    file_put_contents($path, $contents);
                    $size = getimagesize($path);
                    $ratio= $size[1]/$size[0];
                    $scale= 1;
                    $size = $size[1]*$size[0];


                    $ret = CloudCDN::upload($path, $savename);
                    if ($ret) {
                        $upload = sUpload::addNewUpload(
                            $file_name,
                            $savename,
                            $ret,
                            $ratio,
                            $scale,
                            $size,
                            'qiniu'
                        );
                        //添加到待筛选列表
                        $review = sReview::addNewAskReview($upload->id, $file_name);
                    }

                    zip_entry_close($zip_entry);
                }
            }
        }
        zip_close($zip);

        return redirect('/reviewAsk/wait');
    }
}
