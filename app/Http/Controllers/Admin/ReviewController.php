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

        $review = new mReview;
        $user   = new mUser;
        // 检索条件
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
        $orderBy = array($review->getTable().'.create_time desc');

        // 用于遍历修改数据
        $data = $this->page($review, $cond, $join, $orderBy);

        $arr  = array();

        foreach($data['data'] as $key => $row){
            $row_id = $row->id;
            $row->image_url = CloudCDN::file_url($row->savename);
            $row->image_view= Html::image($row->image_url, 'image_view', array('width'=>50));
            $row->avatar    = Html::image($row->avatar, 'avatar', array('width'=>50));
            $row->desc      = $row->labels;

            $row->checkbox  = Form::input('checkbox', 'checkbox', 0, array(
                'class' => 'form-control'
            ));

            $row->puppet_uid= Form::input('text', 'puppet_uid', '', array(
                'class' => 'form-control'
            ));
            $row->upload_id     = Form::input('file', 'upload_id');
            $row->puppet_desc   = Form::input('text', 'desc', '', array(
                'class' => 'form-control'
            ));
            $row->release_time  = Form::input('text', 'release_time', '', array(
                'class' => 'form-control'
            ));


            $arr[] = $row;
        }
        return $this->output_table($data);
    }

    public function set_statusAction(){

        $review_id = $this->post("review_id", "int");
        $status    = $this->post("status", "int");
        $data      = $this->post("data", "string", 0);

        if(!isset($review_id) or !isset($status)){
		    return ajax_return(0, '请选择具体的求助信息');
        }

        $review = Review::findFirst("id=$review_id");
        $old = ActionLog::clone_obj( $review );
        if(!$review){
		    return ajax_return(0, '请选择具体的求助信息');
        }
        // 设置状态为正常，等待定时器触发
        $res = Review::update_status($review, $status, $data);
        if( $res ){
            if( $status == Review::STATUS_DELETED ){
                ActionLog::log(ActionLog::TYPE_DELETE_REVIEW, $old, $res );
            }
            //其他状态呢？
        }

        return ajax_return(1, 'okay');
    }

    public function set_batch_asksAction(){
        $this->_uid = 1;
        $data   = $this->post("data");
        $debug = array();

        $current_key = null;
        $ask_id      = null;
        $review      = null;
        foreach($data as $key=>$row){
            if ($current_key == $row['key']) {
                $type = Review::TYPE_REPLY;
                $review_id  = $ask_id;
            }
            else {
                $type = Review::TYPE_ASK;
                $review_id  = 0;
                $ask_id     = 0;
            }

            $upload = json_decode($row['upload']);
            $upload->savename = $upload->name;

            // key相同，则表示已经有求p，接着是回复
            //$parttime_uid = 0; //todo: session uid
            $parttime_uid = $row['username'];
            $uid = $this->_uid;
            $labels     = $row['label'];
            $row['hour']    = isset($row['hour']) && is_numeric($row['hour'])?$row['hour']: 0;
            $row['min']     = isset($row['min']) && is_numeric($row['min'])?$row['min']: 0;
            $release_time = $row['hour']*3600+$row['min']*60+time();
            if($row['hour'] == 0 && $row['min'] == 0){
                $release_time = time();
            }

            $review = Review::addNewReview($type, $parttime_uid, $uid, $review_id, $labels, $upload, $release_time);

            // 当current key不同，即重新开始计算新的求P的时候
            if ($current_key != $row['key']) {
                $ask_id = $review->id;
            }
            $current_key = $row['key'];
        }
        //pr($debug);

        ajax_return(1, 'okay');
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
        if(!env('dev')) {
            $type = $_FILES["file"]["type"];
            if($type != "application/octet-stream" and $type != "application/zip"){
                pr("zip only");
            }
        }
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

        //todo: location reload
        $this->view->uploads = $uploads;
    }
}
