<?php

namespace Psgod\Models;

use \Psgod\Models\Count;
use \Psgod\Models\Usermeta;

class Comment extends ModelBase
{
    /**
     * 求助的评论
     */
    const TYPE_ASK = 1;

    /**
     * 回复的评论
     */
    const TYPE_REPLY = 2;

    /**
     * 评论的评论
     */
    const TYPE_COMMENT = 3;

    public function getSource()
    {
        return 'comments';
    }

    public function initialize()
    {
        parent::initialize();
        $this->useDynamicUpdate(true);

        $this->belongsTo("uid", "Psgod\Models\User", "uid", array(
            'alias' => 'commenter'
        ));
    }

    /**
     * 更新时间
     */
    public function beforeSave() {
        $this->update_time  = time();

        return $this;
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->create_time  = time();
        $this->status       = self::STATUS_NORMAL;
        $this->ip           = get_client_ip();
        $this->up_count     = 0;
        $this->down_count   = 0;
        $this->inform_count = 0;

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

            $comment = self::findFirst($comment->for_comment);
            $data[] = $comment;
        }

        return $data;
    }

    /**
     * 通过uid数组获取用户的评论信息
     */
    public function get_comments_by_commentids($commentids, $page, $limit){
        $builder = self::query_builder();
        $builder->inWhere('id', $commentids);
        $builder->orderBy('update_time DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * umeng, 通过comment_ids 获取uid发布的评论数量
     */
    public function list_user_comment_count($comment_ids) {

        $builder = self::query_builder();

        $builder->columns('uid, count(1) as num')
            ->inWhere('id', $comment_ids)
            ->andWhere('status = :status:', array('status' => self::STATUS_NORMAL))
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
    public function page($cond, $page=1, $limit=10, $order='new')
    {
        array_push($cond, 'status ='.self::STATUS_NORMAL);
        $builder = self::query_builder();
        $builder->where($cond);
        return self::query_page($builder, $page, $limit);
    }

    public function getHotComments( $target_type, $target_id, $page=1, $size=3 ){
        //todo: Write as config.
        define('MIN_UP_COUNT_FOR_HOT_COMMENT', 10);

        $orderBy = 'create_time DESC';
        return $this->page(array(
                'type='.$target_type,
                'target_id='.$target_id,
                'up_count > '.MIN_UP_COUNT_FOR_HOT_COMMENT
            ),
            $page,
            $size,
            $orderBy
        );
    }

    public function getNewComments( $target_type, $target_id, $page=1, $size=10 ){
        $orderBy = 'up_count DESC';
        return $this->page(array(
                'type='.$target_type,
                'target_id='.$target_id
            ),
            $page,
            $size,
            $orderBy
        );
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
