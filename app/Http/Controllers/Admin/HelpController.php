<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Models\UserScore;
use App\Models\Usermeta;
use App\Models\Ask;
use App\Models\ActionLog;
use App\Models\Reply;
use App\Models\Upload;
use App\Models\Review;
use App\Models\Download;

use App\Models\Label as mLabel,
    App\Models\Ask as mAsk,
    App\Models\User as mUser,
    App\Models\Reply as mReply;

use App\Services\User as sUser,
    App\Services\Reply as sReply,
    App\Services\Download as sDownload;

use Html, Form;

class HelpController extends ControllerBase
{

    private function get_own_users()
    {
        $role = UserRole::find("role_id=".UserRole::ROLE_WORK." or role_id=".UserRole::ROLE_HELP);
        $uids = array();
        foreach($role as $r){
            $uids[] = $r->uid;
        }
        $users = User::find("uid in (".implode(",", $uids).")");
        $this->view->users = $users;
    }

    public function indexAction() {
        $this->get_own_users();
        return $this->output();
    }

    public function waitAction() {
        $this->get_own_users();
        return $this->output();
    }

    public function passAction() {
        $this->get_own_users();
        return $this->output();
    }

    public function rejectAction() {
        return $this->output();
    }

    public function releaseAction() {
        return $this->output();
    }
    public function batchAction() {
        return $this->output();
    }

    public function uploadAction()
    {
        if ($_FILES["file"]["error"] > 0) {
            //pr($_FILES["file"]["error"]);
            //return ajax_return(0, $_FILES["file"]["error"]);
        }
        $type = $_FILES["file"]["type"];
        if($type != "application/octet-stream"){
            //pr("zip only");
            //return ajax_return(0, "zip file only");
        }
        $tmp = APPS_DIR . "tmp/zips/";

        $file_path = $tmp.md5(time().$_FILES["file"]["name"]).".zip";
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);

        $uploads = array();
        //$file_name = "/var/www/clover/apps/tmp/zips/c78c0935c2386fe67cb49ebf48b3514f.zip";
        $zip = zip_open($file_path);
        if ($zip)
        {
            while ($zip_entry = zip_read($zip))
            {
                if (zip_entry_open($zip, $zip_entry))
                {
                    $file_name = iconv('gbk', 'UTF-8', zip_entry_name($zip_entry));
                    $contents = "";
                    while($row = zip_entry_read($zip_entry)){
                        $contents .= $row;
                    }
                    //echo "Name: " . zip_entry_name($zip_entry) . "<br />";
                    //get file name
                    if($contents == "" || sizeof(explode(".", $file_name)) == 1){
                        continue;
                    }
                    //pr($file_name);
                    $savename = $this->cloudCDN->generate_filename_by_file($file_name);

                    $config     = read_config("image");
                    $upload_dir = $config->upload_dir . date("Ym")."/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $path = $upload_dir.$savename;
                    file_put_contents($path, $contents);
                    $size = getimagesize($path);
                    $arr = array();
                    $arr['ratio']  = $size[1]/$size[0];
                    $arr['scale']  = $this->client_width/$size[0];
                    $arr['size']   = $size[1]*$size[0];

                    $ret = $this->cloudCDN->upload($path, $savename);
                    if ($ret) {
                        $upload = \App\Models\Upload::newUpload(
                            $file_name,
                            $savename,
                            $ret,
                            $arr
                        );
                        if( $upload ){
                            ActionLog::log(ActionLog::TYPE_UPLOAD_FILE, array(), $upload);
                        }
                        $uploads[] = $upload;
                    }
                    zip_entry_close($zip_entry);
                }
            }
        }
        zip_close($zip);

        $this->view->uploads = $uploads;
    }

    public function list_usersAction() {
        $user = new User;

        // 检索条件
        $cond = array();
        $cond['uid']        = $this->post("uid", "int");
        $cond['username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );

        // 用于遍历修改数据
        $data  = $this->page($user, $cond);
        foreach($data['data'] as $row){
            $row->reg_time = date('Y-m-d H:i:s',$row->create_time);
            $row->sex   = get_sex_name($row->sex);
            $row->oper  = Html::link('#', '编辑', array(
                'class'=>'edit'
            ));
        }
        // 输出json
        return $this->output_table($data);
    }

    public function list_worksAction(){
        $reply  = new Reply;
        $user   = new User;
        
        // 检索条件
        $cond = array();
        $cond[$reply->getTable().'.status'] = $this->get("status", "int", Reply::STATUS_NORMAL);
        $cond[$reply->getTable().'.id'] = $this->post("id");
        $del_by = $this->post('del_by');
        if( $del_by ){
            $userids = sUser::getFuzzyUserIdsByName($del_by);
            $cond[$reply->getTable().'.del_by'] = array( 
                $user_ids, 
                'IN' 
            );
        }
        $cond[$user->getTable().'.uid'] = $this->post("uid");
        $cond[$user->getTable().'.nickname']   = array(
            $this->post("nickname", "string"),
            "LIKE",
            "AND"
        );
        $cond[$user->getTable().'.username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );

        $join = array();
        $join['User'] = 'uid';

        $orderBy = $this->post('sort','string','id DESC');
        if( stristr($orderBy, 'username') || stristr($orderBy, 'nickname') ){
            $orderBy = array(get_class(new User).'.'.$orderBy);
        }

        $data  = $this->page($reply, $cond, $join, $orderBy);

        foreach($data['data'] as $row){
            $row_id = $row->id;
            $row->avatar = Html::image($row->avatar, 'avatar', array('width'=>50));
            $row->sex    = get_sex_name($row -> sex);

            $row->deleteor = '无';
            if( $row->del_by ){
                $deleteor = sUser::getUserByUid($row->del_by);
                $row->deleteor = $deleteor->username;
            }
            $row->download_times = sDownload::getUserDownloadCount($row->id);
            $row->reply_count    = sReply::getRepliesCountByAskId($row->ask_id);
            #todo: i18n
            $row->status         = ($row -> status) ? "已处理":"未处理";
            $row->create_time    = date('Y-m-d H:i:s', $row->create_time);
            $hostname = env('MAIN_HOST');

            $row->oper = Html::link('#', ' 删除 ', array(
                'class'=>'del',
                'style'=>'color:red',
                'type'=>mLabel::TYPE_REPLY,
                'data'=>$row_id
            ));
            $row->oper .= Html::link("http://$hostname/ask/show/".$row->ask_id, ' 查看原图 ', array(
                'target'=>'_blank',
            ));
            $row->recover = Html::link('#', ' 恢复 ', array(
                'class'=>'recover',
                'style'=>'color:green',
                'type'=>mLabel::TYPE_REPLY,
                'data'=>$row_id
            ));
        }
        return $this->output_table($data);
    }

    public function list_helpsAction(){
        $ask    = new mAsk;
        $user   = new mUser;
        
        $del_by = $this->post('del_by');
        if( $del_by ){
            $userids = sUser::getFuzzyUserIdsByName($del_by);
            $cond[$reply->getTable().'.del_by'] = array( 
                $user_ids, 
                'IN' 
            );
        }

        // 检索条件
        $cond = array();
        $cond[$ask->getTable().'.uid'] = $this->post("uid");
        $cond[$ask->getTable().'.status'] = $this->get("status", "int", Ask::STATUS_NORMAL);
        $cond[$ask->getTable().'.id'] = $this->post("id");
        $cond[$user->getTable().'.uid'] = $this->post("uid");
        $cond[$user->getTable().'.nickname']   = array(
            $this->post("nickname", "string"),
            "LIKE",
            "AND"
        );
        $cond[$user->getTable().'.username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );

        $join = array();
        $join['User'] = 'uid';

        $orderBy = $this->post('sort','string','id DESC');
        if( stristr($orderBy, 'username') || stristr($orderBy, 'nickname') ){
            $orderBy = array($user->getTable().'.'.$orderBy);
        }

        $data  = $this->page($ask, $cond, $join, $orderBy);

        foreach($data['data'] as $row){
            $row_id = $row->id;
            $row->avatar = Html::image($row->avatar, 'avatar', array('width'=>50));
            $row->sex    = get_sex_name($row->sex);
            #$row->content = time_in_ago($row->create_time);
            $row->content = date("Ymd H:i:s", $row->create_time);

            $row->deleteor = '无';
            if( $row->del_by ){
                $deleteor = sUser::getUserByUid($row->del_by);
                $row->deleteor = $deleteor->username;
            }
            $row->download_times = sDownload::getUserDownloadCount($row->id);
            $row->reply_count    = sReply::getRepliesCountByAskId($row->ask_id);
            #todo: i18n
            $row->status         = ($row -> status) ? "已处理":"未处理";
            $row->create_time    = date('Y-m-d H:i:s', $row->create_time);
            $hostname = env('MAIN_HOST');

            $row->oper = Html::link('#', ' 删除 ', array(
                'class'=>'del',
                'style'=>'color:red',
                'type'=>mLabel::TYPE_ASK,
                'data'=>$row_id
            ));
            $row->oper .= Html::link("http://$hostname/ask/show/".$row->ask_id, ' 查看原图 ', array(
                'target'=>'_blank',
            ));
            $row->recover = Html::link('#', ' 恢复 ', array(
                'class'=>'recover',
                'style'=>'color:green',
                'type'=>mLabel::TYPE_ASK,
                'data'=>$row_id
            ));
        }
        return $this->output_table($data);
    }

    public function set_statusAction(){
        $id     = $this->post("id", "int");
        $type   = $this->post("type", "int");
        $status = $this->post("status", "int", Ask::STATUS_DELETED);

        if(!$id or !$type){
            return error('EMPTY_CONTENT');
        }

        if($type == mLabel::TYPE_ASK){
            $ask = sAsk::getAskById($id);
            if(!$ask){
                return error('ASK_NOT_EXIST');
            }
            sAsk::updateAskStatus($ask, $status);
        }
        else {
            $reply = sReply::getReplyById($id);
            if(!$reply){
                return error('REPLY_NOT_EXIST');
            }
            sReply::updateReplyStatus($reply, $status);
            sAsk::updateAskCount($reply->ask_id, 'count', -1);
        }
        return $this->output();
    }




    public function set_asksAction(){
        $this->noview();
        $uids       = $this->post("username");
        $uploads    = $this->post("upload");
        $descs      = $this->post("label");
        $hours      = $this->post("hour");
        $mins       = $this->post("min");

        $upload_objs = array();
        foreach ($uploads as $u) {
            $upload = json_decode($u);
            $upload->savename = $upload->name;
            $upload_objs[] = $upload;
        }
        $result = Ask::addNewAsk($uids[0], $descs[0], $upload_objs[0], set_date($hours[0]*3600+$mins[0]*60+time()), Ask::STATUS_READY);
        if($result){
            ActionLog::log(ActionLog::TYPE_POST_ASK, array(), $result);
            $lbl = Label::addNewLabel(
                $descs[0],
                mt_rand(0, 3)/10,
                mt_rand(0, 3)/10,
                $uids[0],
                0,
                $upload_objs[0]->id,
                $result->id,
                Label::TYPE_ASK
            );
        }

        for($i=1; $i<sizeof($uids); $i++){
            $rr = Reply::addNewReply($uids[$i], $descs[$i], $result->ask_id,  $upload_objs[$i], set_date($hours[$i]*3600+$mins[$i]*60+time()), Ask::STATUS_READY);
            if($rr){
                ActionLog::log(ActionLog::TYPE_POST_REPLY, array(), $rr);
                $lbl = Label::addNewLabel(
                    $descs[$i],
                    mt_rand(0, 3)/10,
                    mt_rand(0, 3)/10,
                    $uids[$i],
                    0,
                    $upload_obj[$i]->id,
                    $rr->id,
                    Label::TYPE_REPLY
                );
            }
        }
        ajax_return(1, 'okay');
    }

    public function set_batch_asksAction(){
        $this->noview();
        $data   = $this->post("data");
        $debug = array();

        $current_key = null;
        $ask_id      = null;
        $review      = null;
        foreach($data as $key=>$row){
            if ($current_key == $row['key']) {
                $type = 1;
                $review_id  = $ask_id;
            }
            else {
                $type = 0;
                $review_id  = 0;
                $ask_id     = 0;
            }

            $upload = json_decode($row['upload']);
            $upload->savename = $upload->name;

            // key相同，则表示已经有求p，接着是回复
            $uid    = $this->_uid;
            $parttime_uid   = $row['username'];
            $labels         = $row['label'];
            $release_time = time() + ($row['hour']*3600+$row['min']*60+time());

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

    /**
     * [testAction 同步reviews的表数据到ask\reply]
     * @return [type] [description]
     */
    public function testAction(){
        $beg_time = time();
        $this->debug_log->log("开始扫描预发布表:");
        $review = Review::get_review_list(time())->toArray();     // 获取预发布review列表

        $ask_count   = 0;
        $reply_count = 0;
        foreach ($review as $v) {
            $upload_obj = Upload::findFirst("id=".$v['upload_id']);
            $review     = Review::findFirst("id = {$v['id']}");

            if ($review->status != Review::STATUS_NORMAL){
                continue;
            }

            if ($v['type'] == Review::TYPE_ASK){        // ask表
                // ask发布成功 更新review状态
                $result = Ask::addNewAsk($v['parttime_uid'], $v['labels'], $upload_obj);
                //todo: 增加标签
                Review::setReviewAskId($review->id, $result->id);
                $review->status = Review::STATUS_RELEASE;
                if($result && $v['labels'] != ''){
                    $lbl = Label::addNewLabel(
                        $v['labels'],
                        mt_rand(0, 3)/10,
                        mt_rand(0, 3)/10,
                        $v['parttime_uid'],
                        0,
                        $upload_obj->id,
                        $result->id,
                        $v['type']
                    );
                }
                $ask_count ++;
            }
            else if($v['ask_id'] > 0){          // reply表
                $result = Reply::addNewReply($v['parttime_uid'], $v['labels'], $v['ask_id'], $upload_obj);
                $review->status = Review::STATUS_RELEASE;
                if($result && $v['labels'] != ''){
                    $lbl = Label::addNewLabel(
                        $v['labels'],
                        mt_rand(0, 3)/10,
                        mt_rand(0, 3)/10,
                        $v['parttime_uid'],
                        0,
                        $upload_obj->id,
                        $result->id,
                        $v['type']
                    );
                }
                $reply_count ++;
            }

            $review->update_time = time();
            $review->save();
        }
        $this->noview();
        echo 'Done!';
        echo "<br>";
        echo 'Ask'.$ask_count;
        echo "<br>";
        echo 'Reply'.$reply_count;
        $this->debug_log->log("结束扫描预发布表,ask:$ask_count,reply:$reply_count,time:".time()-$beg_time);
    }
}
