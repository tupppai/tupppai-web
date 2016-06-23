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
    App\Models\Role as mRole,
    App\Models\Category as mCategory;

use App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\Category as sCategory,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Review as sReview,
    App\Services\Puppet as sPuppet,
    App\Services\User as sUser;
use Html, Form;

class ReviewAskController extends ControllerBase
{
    public $type    = null;
    public $status  = null;

    public function initialize()
    {
        parent::initialize();

        $this->type     = $this->get('type', 'int');
        $this->status   = $this->get('status', 'int', -5);

        view()->share('status', $this->status);
        view()->share('type', $this->type);
    }

    public function waitAction() {
        return $this->output();
    }

    public function passAction() {
        return $this->output();
    }

    public function failAction() {
        return $this->output();
    }

    public function releaseAction() {
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
        $cond[$review->getTable().'.type']    = 1;//$this->type;
        $cond[$review->getTable().'.status']  = $this->status;
        //$cond[$review->getTable().'.uid']  = $this->_uid;

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

        $puppet_arr = array();
        $puppet_ids = [];
        $puppets = sPuppet::getPuppets($this->_uid, [mRole::ROLE_HELP]);
        foreach($puppets as $puppet) {
            $puppet_arr[$puppet['uid']] = $puppet['nickname'].'(uid:'.$puppet['uid'].')';
            $puppet_ids[] = $puppet['uid'];
        }
        $puppet_ids = implode(',', $puppet_ids);
        if($status != mReview::STATUS_HIDDEN) {
            $cond['puppet_uid'] = [ $puppet_ids, 'IN' ];
        }

        // 用于遍历修改数据
        $data = $this->page($review, $cond, $join, $orderBy);

        $arr  = array();

        $categories = sCategory::getCategories()->toArray();
        if( $status == mReview::STATUS_READY ){
            foreach( $categories as $key => $category ){
                $categories[$key]['disabled'] = 'disabled';
            }
        }

        foreach($data['data'] as $key => $row){
            $row_id = $row->id;
            $row->image_url = CloudCDN::file_url($row->savename);
            $row->image_view= Html::image($row->image_url, 'image_view', array('width'=>50));
            $row->avatar    = Html::image($row->avatar, 'avatar', array('width'=>50));
            $row->desc      = $row->labels;
            $row->puppet_uid    = Form::select('puppet_uid',  $puppet_arr, $row->puppet_uid, array(
                'style'=>'width:230px'
            ));
            $row->upload_id     = Form::input('file', 'upload_id');
            /*
            $row->upload_view = '<div>
                                <img width=50 class="user-portrait" src=" ">
                                <input id="upload_'.$row_id.'" type="file" class="form-control" style="left: 85px;top: 7px;">
                                <input name="upload_id" class="hide">
                                </div>';
             */
            $row->puppet_desc   = Form::input('text', 'desc', '', array(
                'class' => 'form-control'
            ));
            $row->execute_time = date( 'Y-m-d H:i:s', $row->release_time );
            $row->release_time  = Form::input('text', 'release_time', date('Y-m-d H:i:s', $row->release_time), array(
                'class' => 'form-control',
                'style' => 'width: 140px'
            ));

            $stac = $categories;
            $row->thread_categories = '';
            $rcatids = array_flip( array_column( $stac, 'id' ) );
            $th_cats = $row->category_ids;
            $th_cats = explode(',', $th_cats);
            if( $th_cats ){
                $thread_categories = [];
                foreach( $th_cats as $cat ){
                    $category = sCategory::detail( sCategory::getCategoryById( $cat ) );
                    $thread_categories[] = '<span class="thread_category">'.$category['display_name'].'</span>';
                    if( isset( $rcatids[$cat] )){
                        $idx = $rcatids[$cat];
                        $stac[ $idx ]['selected'] = 'selected';
                    }
                }
                $row->categories = $stac;
                $row->thread_categories = implode(',', $thread_categories);
            }
            else{
                $row->thread_categories = '无频道';
            }

            $arr[] = $row;
        }
        return $this->output_table($data);
    }

    public function set_statusAction(){
        $review_ids = $this->post("review_ids",'string');
        $status     = $this->post("status", "string", NULL);
        $data       = $this->post("data", "string", 0);

        if( !$review_ids ){
            return error( 'EMPTY_ID' );
        }

        if( is_null($status) ){
            return error( 'EMPTY_STATUS' );
        }
        $r = sReview::updateStatus( $review_ids, $status, $data );

        return $this->output( ['result'=>'ok'] );
    }

    public function set_category_statusAction() {
        $ask_id     = $this->post("ask_id", 'int');
        $category_id= $this->post("category_id", 'int');
        $status     = $this->post("status", 'int');

        if( is_null($ask_id) ){
            return error( 'EMPTY_ID' );
        }
        if( is_null($status) ){
            return error( 'EMPTY_STATUS' );
        }
        if( is_null($category_id) ){
            return error( 'EMPTY_ID' );
        }

        sThreadCategory::setCategory( $this->_uid, mReview::TYPE_ASK, $ask_id, $category_id, $status );

        return $this->output( ['result'=>'ok'] );
    }

    public function set_batch_askAction(){
        $data   = $this->post('data', 'json_str');
        $status = $this->post('status', 'int');

    }

    public function set_batch_replyAction(){
        $data = $this->post('data', 'json_str');
        foreach($data as $key=>$arr) {
            $review = sReview::getReviewById($arr['id']);
            $ask_id = $review->ask_id;
            $uid    = $arr['uid'];
            $desc   = $arr['desc'];
            $review_id      = $arr['id'];
            $upload_id      = $arr['upload_id'];
            $release_time   = strtotime($arr['release_time']);

            sReview::addNewReplyReview($review_id, $ask_id, $uid, $uid, $upload_id, $desc, $release_time);
            //todo: auto publish
            sReview::updateReviewStatus($review_id, mReview::STATUS_DONE);
        }

        return $this->output();
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

    public function update_reviewsAction(){
        $reviews = $this->post('reviews', 'string','');
        $uid = $this->_uid;

        foreach( $reviews as $review ){
            $category_ids = [];
            if( isset( $review['category_ids'] ) ){
                $category_ids = $review['category_ids'];
            }
            sReview::updateReview( $review['id'], $review['release_time'], $review['puppet_uid'], $review['desc'], $uid, $category_ids );
        }

        return $this->output( ['result'=>'ok'] );
    }
}
