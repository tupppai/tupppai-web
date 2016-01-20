<?php namespace App\Http\Controllers\Admin;

use App\Models\Review;

use App\Models\Label as mLabel,
    App\Models\Ask as mAsk,
    App\Models\User as mUser,
    App\Models\UserRole as mUserRole,
    App\Models\Reply as mReply;

use App\Services\User as sUser,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\ActionLog as sActionLog;

use App\Counters\AskDownloads as cAskDownloads,
    App\Counters\AskComments as cAskComments,
    App\Counters\AskShares as cAskShares,
    App\Counters\AskClicks as cAskClicks,
    App\Counters\AskInforms as cAskInforms,
    App\Counters\AskReplies as cAskReplies,
    App\Counters\ReplyUpeds as cReplyUpeds,
    App\Counters\ReplyComments as cReplyComments,
    App\Counters\ReplyShares as cReplyShares,
    App\Counters\ReplyInforms as cReplyInforms,
    App\Counters\ReplyClicks as cReplyClicks;

use Html, Form;

class HelpController extends ControllerBase
{

    private function get_own_users()
    {
        $role = mUserRole::find("role_id=".mUserRole::ROLE_WORK." or role_id=".mUserRole::ROLE_HELP);
        $uids = array();
        foreach($role as $r){
            $uids[] = $r->uid;
        }
        $users = mUser::find("uid in (".implode(",", $uids).")");
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
                            sActionLog::log(sActionLog::TYPE_UPLOAD_FILE, array(), $upload);
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
        $user = new mUser;

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
        $reply  = new mReply;
        $user   = new mUser;
        $status = $this->get("status", "int", '');

        if( $status == '' ){
            $status = implode(',',array_merge(range(-6,-1), range(1,2)));
        }

        // 检索条件
        $cond = array();
        $cond[$reply->getTable().'.status'] = array( $status, 'IN' );
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
            $orderBy = (new mUser)->getTable().'.'.$orderBy;
        }
        $orderBy = [$orderBy];

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
            $row->oper .= Html::link("http://$hostname/#replydetailplay/".$row->ask_id.'/'.$row->id, ' 查看原图 ', array(
                'target'=>'_blank',
            ));
            $row->recover = Html::link('#', ' 恢复 ', array(
                'class'=>'recover',
                'style'=>'color:green',
                'type'=>mLabel::TYPE_REPLY,
                'data'=>$row_id
            ));

            $row->click_count    = cReplyClicks::get($row->id);
            $row->uped_count     = cReplyUpeds::get($row->id);
            $row->comment_count  = cReplyComments::get($row->id);
            $row->share_count    = cReplyShares::get($row->id);
            $row->inform_count   = cReplyInforms::get($row->id);
        }
        return $this->output_table($data);
    }

    public function list_helpsAction(){
        $ask    = new mAsk;
        $user   = new mUser;
        $status = $this->get("status", "int", '');

        if( $status == '' ){
            $status = implode(',',array_merge(range(-6,-1), range(1,2)));
        }


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
        $cond[$ask->getTable().'.id'] = $this->post("id");
        $cond[$ask->getTable().'.uid'] = $this->post("uid");
        $cond[$user->getTable().'.uid'] = $this->post("uid");
        $cond[$ask->getTable().'.status'] = array( $status, 'IN' );
        $cond[$user->getTable().'.nickname'] = array(
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
        $orderBy = [$orderBy];

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
            $row->oper .= Html::link("http://$hostname/#askdetail/ask/".$row->id, ' 查看原图 ', array(
                'target'=>'_blank',
            ));
            $row->recover = Html::link('#', ' 恢复 ', array(
                'class'=>'recover',
                'style'=>'color:green',
                'type'=>mLabel::TYPE_ASK,
                'data'=>$row_id
            ));

            $row->click_count    = cAskClicks::get($row->id);
            $row->comment_count  = cAskComments::get($row->id);
            $row->share_count    = cAskShares::get($row->id);
            $row->download_times = cAskDownloads::get($row->id);
            $row->reply_count    = cAskReplies::get($row->id, 0);
            $row->inform_count   = cAskInforms::get($row->id);
        }
        return $this->output_table($data);
    }

    public function set_statusAction(){
        $id     = $this->post("id", "int");
        $type   = $this->post("type", "int");
        $status = $this->post("status", "int", mAsk::STATUS_DELETED);

        if(!$id or !$type){
            return error('EMPTY_CONTENT');
        }

        if($type == mLabel::TYPE_ASK){
            $ask = sAsk::getAskById($id);
            if(!$ask){
                return error('ASK_NOT_EXIST');
            }
            sAsk::updateAskStatus($ask, $status, $this->_uid);
        }
        else {
            $reply = sReply::getReplyById($id);
            if(!$reply){
                return error('REPLY_NOT_EXIST');
            }
            sReply::updateReplyStatus($reply, $status, $this->_uid);
            sAsk::replyAsk($reply->ask_id, -1);
        }
        return $this->output();
    }




    public function set_asksAction(){
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
        $result = Ask::addNewAsk($uids[0], $descs[0], $upload_objs[0], set_date($hours[0]*3600+$mins[0]*60+time()), mAsk::STATUS_READY);
        if($result){
            sActionLog::log(sActionLog::TYPE_POST_ASK, array(), $result);
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
            $rr = sReply::addNewReply($uids[$i], $descs[$i], $result->ask_id,  $upload_objs[$i], set_date($hours[$i]*3600+$mins[$i]*60+time()), mAsk::STATUS_READY);
            if($rr){
                sActionLog::log(sActionLog::TYPE_POST_REPLY, array(), $rr);
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
            $puppet_uid     = $row['username'];
            $labels         = $row['label'];
            $release_time = time() + ($row['hour']*3600+$row['min']*60+time());

            $review = Review::addNewReview($type, $puppet_uid, $uid, $review_id, $labels, $upload, $release_time);

            // 当current key不同，即重新开始计算新的求P的时候
            if ($current_key != $row['key']) {
                $ask_id = $review->id;
            }
            $current_key = $row['key'];
        }
        //pr($debug);

        ajax_return(1, 'okay');
    }
}
