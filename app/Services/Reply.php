<?php

namespace App\Services;

use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    \App\Models\reply as mAsk,
    \App\Models\Follow as mFollow,
    \App\Models\Comment as mComment,
    \App\Models\Count as mCount,
    \App\Models\Reply as mReply,
    \App\Models\Label as mLabel,
    \App\Models\Record as mRecord,
    \App\Models\Usermeta as mUsermeta,
    \App\Models\Download as mDownload,
    \App\Models\UserRole as mUserRole;

use \App\Services\ActionLog as sActionLog,
    \App\Services\Download as sDownload,
    \App\Services\Count as sCount,
    \App\Services\Label as sLabel,
    \App\Services\Comment as sComment,
    \App\Services\Focus as sFocus,
    \App\Services\UserRole as sUserRole,
    \App\Services\Collection as sCollection,
    \App\Services\User as sUser;

use Queue, App\Jobs\Push;

class Reply extends ServiceBase
{

    /**
     * 添加新回复
     *
     * @param integer $uid        用户ID
     * @param string  $desc       大神带话
     * @param integer $reply_id     求PSID
     * @param \App\Models\Upload $upload_obj 上传对象
     */
    public static function addNewReply($uid, $ask_id, $upload_id, $desc = '', $type = null, $target_id = null)
    {
        if ( !$upload_id ) {
            return error('UPLOAD_NOT_EXIST');
        }
        $upload = sUpload::getUploadById($upload_id);
        if ( !$upload ) {
            return error('UPLOAD_NOT_EXIST');
        }
        $ask    = sAsk::getAskById($ask_id);
        if (!$ask) {
            return error('ASK_NOT_EXIST');
        }

        $reply = new mReply;
        sActionLog::init('POST_REPLY', $reply);

        $status = mReply::STATUS_NORMAL;
        if(sUserRole::checkAuth($uid, mRole::TYPE_PARTTIME)){
            // 兼职逻辑，如果兼职需要审核
            $status = mReply::STATUS_READY;
        }
        else {
            // 如果非兼职，则更新求助的作品数量
            $reply_count = $ask->reply_count + 1;
            $ask->assign(array(
                'reply_count'=>$reply_count,
                'update_time'=>time()
            ));
            $ask->save();
        }

        $reply->assign(array(
            'uid'=>$uid,
            'desc'=>$desc,
            'ask_id'=>$ask_id,
            'upload_id'=>$upload->id,
            'status'=>$status
        ));

        if($type && $target_id) {
            sDownload::uploadStatus(
                $uid,
                $type,
                $target_id,
                $upload->savename
            );
        }

        $reply->save();
        
        #作品推送
        Queue::push(new Push(array(
            'ask_id'=>$ask_id,
            'type'=>'post_reply'
        )));

        sActionLog::save($reply);
        return $reply;
    }

    /**
     * 新建一个定时回复
     *
     * @param integer $uid        用户ID
     * @param string  $desc       大神带话
     * @param integer $reply_id     求PSID
     * @param \App\Models\Upload $upload_obj 上传对象
     * @param string  $time       定的时间 Y-m-d H:i:s
     * @param integer $status     状态
     */
    public static function addNewTimingReply($uid, $desc, $ask_id, $upload, $time, $status=self::STATUS_NORMAL)
    {
        $reply = self::addNewReply($uid,$ask_id, $upload_id, $desc);

        $reply->status  = $status;
        $reply->create_time = $time;
        $reply->update_time = $time;

        sReplymeta::writeMeta($reply->id, mReplymeta::KEY_TIMING, $time);
        $ret = $reply->save();

        //todo: action log
        return $ret;
    }

    public static function userReplyList( $uid, $last_updated, $page, $size ){
        $replyModel = new mReply();
        $replies = $replyModel->get_user_reply( $uid, $page, $size, $last_updated );

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

        // $builder = self::query_builder('r');
        // $asks    = 'Psgod\Models\Ask';
        // return $builder->where('r.status = '.self::STATUS_NORMAL.
        //     " AND r.uid = ".$uid.
        //     " AND r.create_time < ".$last_updated)
        //     //->join($asks, "r.ask_id= r.id", "a", 'left')
        //     //->columns('id, content, x, y, direction')
        //     ->orderBy('r.create_time desc')
        //     ->limit($limit, ($page-1)*$limit)
        //     ->getQuery()
        //     ->execute();


    /**
     * 获取作品数量
     */
    public static function getRepliesCountByAskId($ask_id) {
        return (new mReply)->count_replies_by_askid($ask_id);
    }

    public static function getRepliesByAskId($ask_id, $page, $size) {
        $mReply = new mReply;

        $replies    = $mReply->get_replies_by_askid($ask_id, $page, $size);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

    /**
     * [get_user_scores 获取评分]
     */
    public static function getReplyScore($uid, $reply_id)
    {
        $mUserScore = new mUserScore;

        $score = $mUserScore->getReplyScore($uid, $reply_id);
        return $score;
    }

    /**
     * 获取作品收藏列表
     */
    public static function getCollectionReplies($uid, $page, $limit) {
        $mCollection = new mCollection;
        $mReply      = new mReply;

        $collectionReplies = $mCollection->getCollectionReplies($uid);

        $collectionReplyids= array();
        foreach($collectionReplies as $collection) {
            $collectionReplyids[] = $collection->reply_id;
        }

        $replies = $mReply->get_replies_by_replyids($collectionReplyids, $page, $limit);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

    /**
     * 获取用户求p数量
     */
    public static function getUserReplyCount ( $uid ) {
        return (new mReply)->count_user_reply($uid);
    }

    /**
     * 获取各种数量
     */
    public static function getReplyCount ( $uid, $count_name ) {
        $reply = new mReply;
        $count_name  = $count_name.'_count';
        if (!property_exists($reply, $count)) {
            return error('WRONG_ARGUMENTS');
        }

        return mReply::sum(array(
            'column'     =>$count_name,
            'conditions' =>"uid = {$uid}"
        ));
    }

    /**
     * 数量变更
     */
    public static function updateReplyCount($target_id, $count_name, $status){
        $type  = mReply::TYPE_REPLY;
        $count = sCount::updateCount($target_id, $type, $count_name, $status);
        //todo: 是否需要报错提示,不需要更新
        if (!$count)
            return false;

        $reply  = mReply::find($target_id);
        if (!$reply)
            return error('REPLY_NOT_EXIST');
        $count_name  = $count_name.'_count';
        if(!isset($reply->$count_name)) {
            return error('WRONG_ARGUMENTS');
        }

        if ($count->status == mCount::STATUS_NORMAL)
            $value = 1;
        else
            $value = -1;

        $reply->$count_name += $value;
        if ($reply->$count_name < 0)
            $reply->$count_name = 0;

        // 通过名字获取日志记录的键值
        $name   = ( $value==1? '': 'CANCEL_' ).strtoupper($count_name).'_REPLY';
        $key    = sActionLog::getActionKey($name);

        $ret    = $reply->save();
        sActionLog::log($key, $reply, $ret);

        return $ret;
    }

    /**
     * 更新作品审核状态
     */
    public static function updateReplyStatus($reply, $status, $data="")
    {
        $mUserScore = new mUserScore;

        $reply->status = $status;
        $uid        = $reply->uid;
        $reply_id   = $reply->id;

        switch($status){
        case self::STATUS_NORMAL:
            $mUserScore->update_score($uid, UserScore::TYPE_REPLY, $reply_id, $data);
            reply::set_reply_count($reply->ask_id);
            break;
        case self::STATUS_READY:
            break;
        case self::STATUS_REJECT:
            $reply->del_by = $uid;
            $reply->del_time = time();
            $UserScore->update_content($uid, UserScore::TYPE_REPLY, $reply_id, $data);
            break;
        case self::STATUS_BLOCKED:
            break;
        case self::STATUS_DELETED:
            $reply->del_by = $uid;
            $reply->del_time = time();
            break;
        }

        $ret = $reply->save();
        return $ret;
    }

    /**
     * 获取标准输出(含评论&作品
     */
    public static function detail( $reply ) {

        $uid    = _uid();
        $width  = _req('width', 480);

        $data = array();
        $data['id']             = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['type']           = mLabel::TYPE_REPLY;

        $data['comments']       = sComment::getComments(mComment::TYPE_REPLY, $reply->id, 0, 5);
        $data['labels']         = sLabel::getLabels(mLabel::TYPE_REPLY, $reply->id, 0, 0);

        $data['is_download']    = sDownload::hasDownloadedAsk($uid, $reply->id);
        $data['uped']           = sCount::hasOperatedAsk($uid, $reply->id, 'up');
        $data['collected']      = sCollection::hasCollectedReply($uid, $reply->id);

        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex;
        $data['uid']            = $reply->replyer->uid;
        $data['nickname']       = $reply->replyer->nickname;

        $data['upload_id']      = $reply->upload_id;
        $data['create_time']    = $reply->create_time;
        $data['update_time']    = $reply->update_time;
        $data['desc']           = $reply->desc;
        $data['up_count']       = $reply->up_count;
        $data['comment_count']  = $reply->comment_count;
        $data['click_count']    = $reply->click_count;
        $data['inform_count']   = $reply->inform_count;

        $data['share_count']    = $reply->share_count;
        $data['weixin_share_count'] = $reply->weixin_share_count;

        $upload = $reply->upload;
        $data['image_width']    = $width;
        if( $upload && $upload->ratio ) {
            $data['image_height']   = intval( $width * 1.333 );
        }
        else {
            $data['image_height']   = intval( $width * $upload->ratio );
        }
        //$data['image_url']      = \CloudCDN::file_url($upload->savename, $width);

        return $data;
    }











    // system message
    public static function updateMsg( $uid, $last_updated ){

        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_REPLY );
        $lasttime = $lasttime?$lasttime[Usermeta::KEY_LAST_READ_REPLY]: 0;

        $builder = Reply::query_builder('r');
        $where = array(
            'r.create_time < '.$last_updated,
            'r.create_time > '.$lasttime,
            'r.status='.Reply::STATUS_NORMAL,
            'a.uid='.$uid
        );

		$reply = 'App\Models\Ask';
        $res = $builder -> where( implode(' AND ',$where) )
                        -> join($reply, 'a.id=r.ask_id', 'a', 'left')
                        -> getQuery()
                        -> execute();
        $replies = self::query_page($builder)->items;
        foreach( $replies as $row){
            Message::newReply(
                $row->uid,
                $uid,
                'uid:'.$row->uid.' huifu le ni de qiuzhu.',
                $row->reply_id
            );
        }

        if(isset($row)){
            Usermeta::refresh_read_notify(
                $uid,
                Usermeta::KEY_LAST_READ_REPLY,
                $row->create_time
            );
        }

        return $replies;
    }

    public static function count_unread_reply( $uid){
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_REPLY );
        if( $lasttime ){
            $lasttime = $lasttime[Usermeta::KEY_LAST_READ_REPLY];
        }
        else{
            $lasttime = 0;
        }

        $builder = Reply::query_builder('r');
        $where = array(
            'r.create_time>'.$lasttime,
            'r.status='.Reply::STATUS_NORMAL,
            'a.uid='.$uid
        );
        $reply = 'App\Models\Ask';

        $res = $builder -> where( implode(' AND ',$where) )
                        -> join($reply, 'a.id=r.ask_id', 'a', 'left')
                        -> columns('count(r.id) as c')
                        -> getQuery()
                        -> execute();
        return $res['c']->toArray()['c'];
    }

    public static function list_unread_replies( $lasttime, $page = 1, $size = 500 ){

        $reply = new self;
        $sql = 'select a.uid, count(1) as num'.
            ' FROM replies r'.
            ' LEFT JOIN replys a ON r.ask_id = a.id'.
            ' WHERE r.status='.self::STATUS_NORMAL.
            ' AND a.status='.self::STATUS_NORMAL.
            ' AND r.create_time>'.$lasttime.
            ' GROUP BY a.uid';
        return new Resultset(null, $reply, $reply->getReadConnection()->query($sql));
    }

}
