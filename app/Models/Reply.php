<?php namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    App\Models\Record,
    App\Models\Count,
    App\Models\Usermeta,
    App\Models\Label;
use App\Models\Label as LabelBase;
use DB;

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

    public function get_reply_by_upload_id($upload_id){
        return self::where('upload_id', $upload_id)->first();
    }

    /**
     * 通过ask_id获取作品数量
     */
    public function count_replies_by_askid($ask_id, $uid = NULL ) {
        $query = $this->where(function( $q ) use ( $uid ){
            if( $uid ){
                    $q->where('uid', $uid );
            }
        });
        return $query->where('status', '>', self::STATUS_DELETED )
                    ->where('ask_id', $ask_id)
                    ->count();
    }

    /**
     * 通过ask_id获取作品列表
     */
    public function get_replies_by_askid($ask_id, $page, $limit) {
        $builder = self::query_builder();
        $builder = $builder->where('ask_id', $ask_id)
            ->blocking(_uid())
            ->orderBy('update_time', 'DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 通过uid获取作品列表
     */
    public function get_replies_by_uid($uid) {
        $builder = self::query_builder();
        $builder = $builder->where('uid', $uid);
        return $builder->get();
    }

    /**
     * 通过ask_id获取作品列表除开reply_id的列表
     */
    public function get_ask_replies_without_replyid($ask_id, $reply_id, $page, $limit) {
        $builder = self::query_builder();
        $builder = $builder->blocking(_uid())
            ->where('ask_id', $ask_id)
            ->where('id', '!=', $reply_id);
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 通过id数组获取用户的求助信息
     */
    public function get_replies_by_replyids($replyids, $page, $limit){
        $builder = self::query_builder();
        $builder->whereIn('id', $replyids);

        return self::query_page($builder, $page, $limit);
    }

    /**
     * 计算用户发的作品数量
     */
    public function count_user_reply($uid) {
        $builder = self::query_builder();
        return $builder->where('replies.uid', $uid)
                    ->leftjoin( 'asks', 'asks.id', '=', 'replies.ask_id')
                    ->where('asks.status' ,'>', self::STATUS_DELETED)
                    ->count();
    }

    public function get_replies($cond= array(), $page, $limit=0, $uid = 0)
    {
        $builder = self::query_builder();

        // 过滤被删除到帖子
        $builder = $builder->select('replies.*');

        if(isset($cond['ask_id'])){
            $builder = $builder->where('ask_id', $cond['ask_id']);
        }
        if(isset($cond['uid'])){
            $builder = $builder->where('uid', $cond['uid']);
        }

        $id_arrs = $this->from('thread_categories')
                ->where( function( $q ) use ( $cond ){
                    if( isset($cond['category_id']) ){
                        $q = $q->where('category_id', '=', $cond['category_id']);
                    }
                })
                ->where('category_id', '!=', self::CATEGORY_TYPE_TIMELINE)
                ->where('target_type', '=', self::TYPE_REPLY)
                ->where('status', '>', self::STATUS_DELETED)
                ->select('target_id')
                ->get();
        $ids = $id_arrs->pluck('target_id')->toArray();
        $builder = $builder->whereIn('id', $ids )
                ->where('status', '>', self::STATUS_DELETED);

        return self::query_page($builder, $page, $limit);
    }

    //区别在与加了动态
    public function get_user_replies($cond= array(), $page, $limit=0, $uid = 0)
    {
        $builder = self::query_builder();

        // 过滤被删除到帖子
        $builder = $builder->select('replies.*');

        if(isset($cond['ask_id'])){
            $builder = $builder->where('ask_id', $cond['ask_id']);
        }
        if(isset($cond['uid'])){
            $builder = $builder->where('uid', $cond['uid']);
        }

        $id_arrs = $this->from('thread_categories')
                ->where('target_type', '=', self::TYPE_REPLY)
                ->where('status', '>', self::STATUS_DELETED)
                ->select('target_id')
                ->get();
        $ids = $id_arrs->pluck('target_id')->toArray();
        $builder = $builder->whereIn('id', $ids )
                ->where('status', '>', self::STATUS_DELETED);

        return self::query_page($builder, $page, $limit);
    }

    public function get_replies_v2($cond= array(), $page, $limit=0, $uid = 0)
    {
        $builder = self::query_builder();

        // 过滤被删除到帖子
        $builder = $builder->select('replies.*');

        if(isset($cond['ask_id'])){
            $builder = $builder->where('ask_id', $cond['ask_id']);
        }
        if(isset($cond['uid'])){
            $builder = $builder->where('uid', $cond['uid']);
        }

        $id_arrs = $this->from('thread_categories')
            ->where( function( $q ) use ( $cond ){
                if( isset($cond['category_id']) ){
                    $q = $q->where('category_id', '=', $cond['category_id']);
                }
            })
            ->where('category_id', '!=', self::CATEGORY_TYPE_TIMELINE)
            ->where('target_type', '=', self::TYPE_REPLY)
            ->where('status', '>', self::STATUS_DELETED)
            ->select('target_id')
            ->get();
        $ids = $id_arrs->pluck('target_id')->toArray();
        $builder = $builder->whereIn('id', $ids )
            ->where('status', '>', self::STATUS_DELETED);

        return self::query_page($builder, $page, $limit);
    }

    public static function query_builder($alias = '')
    {
        $class = get_called_class();

        $builder = new $class;
        $table_name = 'replies';
        $builder = $builder ->lastUpdated()
            ->orderBy($table_name.'.create_time', 'DESC');

        /*
        //列表页面需要屏蔽别人的广告贴，展示自己的广告贴
        $builder->where(function($query)  {
            $uid = _uid();
            $query = $query->valid();
            //加上自己的广告贴
            if( $uid ) {
                $query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
            }
        });
         */

        return $builder;
    }


    /**
     * 消息用，需要记录上次拉取时间
     */
    public function get_replies_of_asks( $ask_ids, $last_fetch_time ){
        $builder = self::query_builder();

        $builder = $builder->whereIn('ask_id', $ask_ids)
            ->where('create_time', '>=', $last_fetch_time)
            ->orderBy('update_time', 'ASC');

        return $builder->get();
    }

    public function change_replies_status( $uid, $to_status, $from_status = '' ){
        $cond = [
            'uid' => $uid
        ];
        if( !$from_status ){
            $cond['status']=$from_status;
        }
        return $this->where( $cond )->update(['status'=> $to_status]);
    }

    //包括被屏蔽的，删除的，全部。
    public function get_all_replies_by_ask_id( $ask_id, $page = 1, $size =15 ){
        $query = $this->where('ask_id', $ask_id);
        if( $page && $size ){
            $query = $query->forPage( $page, $size );
        }
        return $query->get();
    }

    //通过askID获取第一个作品
    public function get_first_reply($ask_id)
    {
        return $this->where('ask_id',$ask_id)
            ->where('status','>',self::STATUS_DELETED)
            ->first();
    }

    //通过askID获取最后一个作品
    public function get_last_reply($ask_id)
    {
        return $this->where('ask_id',$ask_id)
            ->where('status','>',self::STATUS_DELETED)
            ->orderBy('create_time', 'DESC')
            ->first();
    }

    /**
     * ask 下状态正常的全部作品。
     */
    public function get_normal_all_replies_by_ask_id( $ask_id){
        return $this->where('ask_id', $ask_id)
            ->where('status','>',self::STATUS_DELETED)
            ->get();
    }

    public function sum_clicks_by_reply_ids( $replyIds ){
        return $this->whereIn('id', $replyIds)
                    ->where('status', '>=', self::STATUS_DELETED)
                    ->sum('click_count');
    }

    public function get_reply_ids_by_uid( $uid, $page = null, $size = null ){
        $builder = self::query_builder();
        $builder = $builder->where('uid', $uid);
        if( $size ){
            return $builder->forPage( $page, $size )
                            ->lists('id');
        }
        else{
            return $builder->lists('id');
        }
    }

    public function count_users_by_reply_ids( $replyIds ){
        return $this->whereIn( 'id', $replyIds )
                    ->groupBy( 'uid' )
                    ->count();
    }
}
