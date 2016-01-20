<?php

namespace App\Models;

use \App\Models\Count;
use \App\Models\Usermeta;

class Comment extends ModelBase
{
    protected $table = 'comments';

    /**
     * 绑定映射关系
     */
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
     * 统计评论数量
     */
    public function count_comments($type, $id) {
        return self::where('type', $type)
            ->where('target_id', $id)
            ->where('status', self::STATUS_NORMAL)
            ->count();
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

    public function getHotComments( $target_type, $target_id, $page=1, $size=3 ){
        //todo: Write as config.
        //todo: for debug
        $MIN_UP_COUNT_FOR_HOT_COMMENT = 0; //10;
        $builder = $this->where('type', $target_type)
            ->where(function($query){
                $uid = _uid();
                //加上自己的广告贴
                $query = $query->where('comments.status','>', self::STATUS_DELETED );
                if( $uid ){
                    $query = $query->orWhere([ 'comments.uid'=>$uid, 'comments.status'=> self::STATUS_BLOCKED ]);
                }
            })
            ->where('target_id', $target_id)
            ->where('up_count', '>=', $MIN_UP_COUNT_FOR_HOT_COMMENT)
            ->orderBy('up_count', 'desc')
            ->forPage( $page, $size );

        return $builder->get();
    }

    public function getNewComments( $target_type, $target_id, $page=1, $size=10 ){
        $builder = $this->where('type', $target_type)
            ->where(function($query){
                $uid = _uid();
                //加上自己的广告贴
                $query = $query->where('comments.status','>', self::STATUS_DELETED );
                if( $uid ){
                    $query = $query->orWhere([ 'comments.uid'=>$uid, 'comments.status'=> self::STATUS_BLOCKED ]);
                }
            })
            ->where('target_id', $target_id)
            ->orderBy('create_time', 'desc')
            ->forPage( $page, $size );
        //屏蔽用户
        $builder = $builder->blockingUser(_uid());
        return $builder->get();
    }

    //remove---------
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
    //remove--------

    public function get_commments_by_uid( $uid, $page, $size ){
        return $this ->where( 'uid', $uid )
                     ->valid()
                     ->orderBy( 'create_time', 'DESC' )
                     ->forPage( $page, $size )
                     ->get();
    }

    public function change_user_comments_status( $uid, $to_status, $from_status ){
        $cond = [
            'uid' => $uid
        ];
        if( !$from_status ){
            $cond['status']=$from_status;
        }
        return $this->change_comments_status( $cond, $to_status );
    }
    private function change_comments_status( $cond, $to_status ){
        return $this->where( $cond )->update(['status'=> $to_status]);
    }

    public function change_comment_status( $id, $to_status ){
        $cond = ['id' => $id];
        return $this->change_comments_status( $cond, $to_status );
    }
    /**
     * 获取评论列表
     */
    //public function get_comments($type, $target_id, $page=1, $size=10) {
    //public static function comment_page($type, $target_id, $page=1, $limit=10, $order='new', $keys=array())
    //public function list_user_comment_count($comment_ids){
}
