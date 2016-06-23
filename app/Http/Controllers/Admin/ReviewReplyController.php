<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Usermeta;
use App\Models\Role;
use App\Models\Upload;
use App\Models\ActionLog;

use App\Facades\CloudCDN;

use App\Models\Review as mReview,
    App\Models\User as mUser,
    App\Models\Puppet as mPuppet,
    App\Models\Role as mRole;

use App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\Review as sReview,
    App\Services\Reply as sReply,
    App\Services\Puppet as sPuppet,
    App\Services\Category as sCategory,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\User as sUser;
use Html, Form;

class ReviewReplyController extends ControllerBase
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
        $this->type     = $this->get('type', 'int', mReview::TYPE_REPLY);
        $this->status   = $this->get('status', 'int', -5);

        view()->share('status', $this->status);
        view()->share('type', $this->type);
        view()->share('users', $users);
    }

    public function waitAction() {
        return $this->output();
    }

    public function passAction() {
        return $this->output();
    }

    public function replyAction() {
        return $this->output();
    }

    public function failAction() {
        return $this->output();
    }

    public function releaseAction() {
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
        $puppet   = new mPuppet;
        // 检索条件
        //作品上传页面需要将已经发出去的求助拉出来
        if($this->type == mReview::TYPE_REPLY && $this->status == mReview::STATUS_HIDDEN) {
            $this->type     = mReview::TYPE_ASK;
            $this->status   = 1;
        }
        $cond[$review->getTable().'.type']    = $this->type;
        $cond[$review->getTable().'.status']  = $this->status;

        if( $username ){
            $cond[$user->getTable().'.username'] = array(
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
        if( $status == mReview::STATUS_READY ){
            $orderBy = array($review->getTable().'.release_time ASC');
        }
        else{
            $orderBy = array($review->getTable().'.release_time DESC');
        }

        $work_puppet_arr = array();
        $help_puppet_ids = [];
        $puppets = sPuppet::getPuppets($this->_uid, [mRole::ROLE_WORK]);
        foreach($puppets as $puppet) {
            $work_puppet_arr[$puppet['uid']] = $puppet['nickname'].'(uid:'.$puppet['uid'].')';
        }
        $puppets = sPuppet::getPuppets($this->_uid, [mRole::ROLE_HELP]);
        foreach( $puppets as $puppet ){
            $help_puppet_ids[] = $puppet['uid'];
        }
        $help_puppet_ids = implode(',', $help_puppet_ids);
        $cond['reviews.puppet_uid'] = [ $help_puppet_ids, 'IN' ];

        // 用于遍历修改数据
        $data = $this->page($review, $cond, $join, $orderBy);

        $arr  = array();

        $categories = sCategory::getCategories();

        foreach($data['data'] as $key => $row){
            $row_id = $row->id;
            $row->categories = '';

            //$th_cats = sThreadCategory::getCategoriesByTarget( $row->type, $row->ask_id );
            //get 不到 新增的reply_id
            if( false/*!$th_cats->isEmpty()*/ ){
                $thread_categories = [];
                foreach( $th_cats as $cat ){
                    $category = sCategory::detail( sCategory::getCategoryById( $cat->category_id ) );
                    switch ( $cat->status ){
                        case mCategory::STATUS_NORMAL:
                            $class = 'normal';
                            break;
                        case mCategory::STATUS_CHECKED:
                            $class = 'verifing';
                            break;
                        case mCategory::STATUS_DONE:
                            $class = 'verified';
                            break;
                        case mCategory::STATUS_DELETED:
                            $class = 'deleted';
                            break;
                    }
                    $thread_categories[] = '<span class="thread_category '.$class.'">'.$category['display_name'].'</span>';
                }
                $row->categories = implode(',', $thread_categories);
            }
            else{
                $row->categories = '无频道';
            }

            $row->image_url = CloudCDN::file_url($row->savename);
            $row->image_view= Html::image($row->image_url, 'image_view', array('width'=>50));
            $row->avatar    = Html::image($row->avatar, 'avatar', array('width'=>50));
            $row->desc      = $row->labels;

            $row->checkbox  = Form::input('checkbox', 'checkbox', 0, array(
                'class' => 'form-control'
            ));

            $row->puppet_uid    = Form::select('puppet_uid',  $work_puppet_arr, $row->puppet_uid, array(
                'style'=>'width:230px'
            ));
            $row->upload_id     = Form::input('file', 'upload_id');
            $row->upload_view = '<div>
                                <img id="preview_'.$row->id.'"width=50 class="user-portrait" data-id="'.$row->id.'">
                                <input id="upload_'.$row->id.'"name="upload_id" class="hide">
                                </div>';
            //<input id="upload_'.$row_id.'" type="file" class="form-control" style="left: 85px;top: 7px;">
            $row->puppet_desc   = Form::input('text', 'desc', '', array(
                'class' => 'form-control'
            ));
            $row->execute_time  = date( 'Y-m-d H:i', $row->release_time );
            $row->release_time  = Form::input('text', 'release_time', $row->release_time, array(
                'class' => 'form-control',
                'style' => 'width: 140px'
            ));

            $arr[] = $row;
        }
        return $this->output_table($data);
    }

    public function set_statusAction(){
        $review_ids = $this->post('ids', 'string','');
        $status = $this->post('status', 'string');
        switch( $status ){
            case 'delete':
                $status = mReview::STATUS_DELETED;
                break;
            case 'hide':
                $status = mReview::STATUS_BANNED;
                break;
            default:
                break;
        }
        $review = sReview::updateStatus($review_ids, $status);

        return $this->output_json( ['result'=>'ok'] );
    }

    public function set_category_statusAction() {
        $reply_id   = $this->post("reply_id", 'int');
        $category_id= $this->post("category_id", 'int');
        $status     = $this->post("status", 'int');

        if( is_null($reply_id) ){
            return error( 'EMPTY_ID' );
        }
        if( is_null($status) ){
            return error( 'EMPTY_STATUS' );
        }
        if( is_null($category_id) ){
            return error( 'EMPTY_ID' );
        }

        sThreadCategory::setCategory( $this->_uid, mReview::TYPE_REPLY, $reply_id, $category_id, $status );

        return $this->output( ['result'=>'ok'] );
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

    public function set_batch_replyAction(){
        $data = $this->post('data', 'json_str');
        if(empty($data)) {
            return error('WRONG_ARGUMENTS', '请选择求助信息');
        }
        $review_ids = [];
        $uid  = $this->_uid;
        foreach($data as $key=>$arr) {
            $release_time   = strtotime($arr['release_time']);
            if( $arr['review_id'] ){
                $review_id = $arr['review_id'];
                $review = sReview::getReviewById( $review_id );
                $ask_id = $review->ask_id;
                if( $release_time < $review->release_time ){
                    return error( 'RELEASING_BEFORE_ASK', '作品内容不能早于求助发布' );
                }
            }
            else{
                $ask_id = $arr['id'];
                $review_id = 0;
            }
            $desc = $arr['desc'];
            $puppet_uid = $arr['uid'];
            $upload_id = $arr['upload_id'];

            $r = sReview::addNewReplyReview($review_id, $ask_id, $uid, $puppet_uid, $upload_id, $desc, $release_time);
            $review_ids[] = $r->id;

        }
        $category_ids = array_column( $data, 'category_ids' );
        sReview::updateStatus( $review_ids, mReview::STATUS_READY, '', $category_ids);


        return $this->output_json(['result'=>'ok']);
    }

    public function udpate_reviewsAction(){
        $reviews = $this->post('reviews', 'string','');

        foreach( $reviews as $review ){
            sReview::updateReview( $review['id'], $review['release_time'], $review['puppet_uid'], $review['desc'] );
        }

        return $this->output_json( ['result'=>'ok'] );
    }
}
