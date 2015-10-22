<?php

namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    \App\Models\Record,
    \App\Models\Count,
    \App\Models\Usermeta,
    \App\Models\Label;
use App\Models\Label as LabelBase;

class Reply extends ModelBase
{
    protected $table = 'replies';

    /**
     * 绑定映射关系
     */
    public function replyer() {
        return $this->belongsTo('App\Models\User', 'uid');
    }
    public function upload() {
        return $this->hasOne('App\Models\Upload', 'id', 'upload_id');
    }

    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;
        $this->type         = self::TYPE_NORMAL;
        $this->ip           = get_client_ip();

        return $this;
    }

    public function get_reply_by_id($reply_id){
        return self::find($reply_id);
    }

    /**
     * 通过ask_id获取作品数量
     */
    public function count_replies_by_askid($ask_id) {
        return self::where('ask_id', $ask_id)
            ->where('status', self::STATUS_NORMAL)
            ->count();
    }

    /**
     * 通过ask_id获取作品列表
     */
    public function get_replies_by_askid($ask_id, $page, $limit) {
        $builder = self::query_builder();
        $builder = $builder->where('ask_id', $ask_id)
            ->orderBy('update_time', 'DESC');
        return self::query_page($builder, $page, $limit);
    }
    
    /**
     * 通过ask_id获取作品列表除开reply_id的列表
     */
    public function get_ask_replies_without_replyid($ask_id, $reply_id, $page, $limit) {
        $builder = self::query_builder();
        $builder = $builder->where('ask_id', $ask_id)
            ->where('id', '!=', $reply_id)
            ->orderBy('update_time', 'DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 通过id数组获取用户的求助信息
     */
    public function get_replies_by_replyids($replyids, $page, $limit){
        $builder = self::query_builder();
        $builder->whereIn('id', $replyids);
        $builder->orderBy('update_time');

        return self::query_page($builder, $page, $limit);
    }

    /**
     * 计算用户发的作品数量
     */
    public function count_user_reply($uid) {
        $count = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
    * 分页方法
    *
    * @param int 加数
    * @param int 被加数
    * @return integer
    */
    public function page($keys = array(), $page=1, $limit=10, $type='new')
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            if($v)
                $builder = $builder->where($k, '=', $v);
        }
        $builder = $builder->where('status', '!=', self::STATUS_DELETED);
        /*
        if($type == 'new')
            $builder = $builder->orderBy('update_time', 'DESC');
        else
            $builder = $builder->orderBy('click_count', 'DESC');
         */
        $builder = $builder->orderBy('id', 'DESC');

        return self::query_page($builder, $page, $limit);
    }

    public static function user_get_reply_page($uid, $page=1, $limit=15){
        $builder = self::query_builder('r');
        $upload  = 'App\Models\Upload';
        $builder->join($upload, 'up.id = r.upload_id', 'up')
                ->where("r.uid = {$uid} and r.status = ".self::STATUS_NORMAL)
                ->columns(array('r.id', 'r.ask_id',
                    'up.savename', 'up.ratio', 'up.scale'
                ));
        return self::query_page($builder, $page, $limit);
    }

    public function get_user_reply($uid, $page, $limit, $last_read_time=NULL ){
        $builder = self::query_builder();

        if( !is_null( $last_read_time) ){
            $last_read_time = time();
        }

        $builder = $builder->where('uid', $uid)->valid()
            ->where('update_time','<', $last_read_time )
            ->orderBy('update_time', 'DESC');

        return self::query_page($builder, $page, $limit);
    }

    public function get_replies_of_asks( $ask_ids, $last_fetch_msg_time ){
        return $this->where([
            'status' => self::STATUS_NORMAL
            ])
        ->where('update_time', '>', $last_fetch_msg_time )
        ->whereIn('ask_id', $ask_ids )
        ->orderBy('update_time', 'ASC')
        ->get();
    }
}
