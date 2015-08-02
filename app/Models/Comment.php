<?php

namespace App\Models;

use \App\Models\Count;
use \App\Models\Usermeta;

class Comment extends ModelBase
{
    protected $table = 'comments';

    public function initialize()
    {
        parent::initialize();
        $this->useDynamicUpdate(true);

        $this->belongsTo("uid", "App\Models\User", "uid", array(
            'alias' => 'commenter'
        ));
    }

    public function commenter() {
        return $this->belongsTo('App\Models\User', 'uid', 'uid');
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;
        $this->ip           = get_client_ip();

        return $this;
    }

    public function get_at_comments($level = 2) {
        $data = array();
        $comment = $this;
        $count   = 0;

        while (TRUE) {
            if ( !isset($comment->for_comment) || !$comment->for_comment )
                break;
            if ( $count ++ > $level )
                break;

            $comment = self::find($comment->for_comment);
            $data[] = $comment;
        }

        return $data;
    }

    /**
     * 通过id获取comment
     */
    public function get_comment_by_id($id) {
        $comment = self::find($id);

        return $comment;
    }

    /**
     * 通过uid数组获取用户的评论信息
     */
    public function get_comments_by_commentids($commentids, $page, $limit){
        $builder = self::query_builder();
        $builder = $builder->whereIn('id', $commentids)
            ->orderBy('update_time', 'DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * umeng, 通过comment_ids 获取uid发布的评论数量
     */
    public function list_user_comment_count($comment_ids) {

        $builder = self::query_builder();

        $builder = $builder->select('uid, count(1) as num')
            ->whereIn('id', $comment_ids)
            ->where('status', self::STATUS_NORMAL)
            ->groupBy('uid');

        return self::query_page($builder);
    }

    /**
    * 分页方法
    *
    * @param int 加数
    * @param int 被加数
    * @return integer
    */
    public function page($keys, $page=1, $limit=10, $order='new')
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, '=', $v);
        }
        $builder = $builder->orderBy($order, 'desc');

        return self::query_page($builder, $page, $limit);
    }

    public function getHotComments( $target_type, $target_id, $page=1, $size=3 ){
        //todo: Write as config.
        $MIN_UP_COUNT_FOR_HOT_COMMENT = 10;
        $builder = self::query_builder();
        $builder = $builder->where('type', $target_type)
            ->where('target_id', $target_id)
            ->where('up_count', '>', $MIN_UP_COUNT_FOR_HOT_COMMENT)
            ->orderBy('create_time', 'desc');
        return self::query_page($builder, $page, $size);
    }

    public function getNewComments( $target_type, $target_id, $page=1, $size=10 ){
        $builder = self::query_builder();
        $builder = $builder->where('type', $target_type)
            ->where('target_id', $target_id)
            ->orderBy('up_count', 'desc');
        return self::query_page($builder, $page, $size);
    }

    public function getUnreadMessages( $uid, $last_fetch_time, $last_read_msg_time){
        $builder = self::query_builder('c');
        $where = array(
            'c.create_time < '.$last_fetch_time,
            'c.create_time > '.$last_read_msg_time,
            'c.status='.Reply::STATUS_NORMAL,
            'c.reply_to='.$uid
        );

        return $this->page( implode(' AND ',$where) , $page, $size );
    }


    /**
     * 获取评论列表
     */
    //public function get_comments($type, $target_id, $page=1, $size=10) {
    //public static function comment_page($type, $target_id, $page=1, $limit=10, $order='new', $keys=array())
    //public function list_user_comment_count($comment_ids){
}
