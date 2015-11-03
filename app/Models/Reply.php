<?php namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    App\Models\Record,
    App\Models\Count,
    App\Models\Usermeta,
    App\Models\Label;
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
        $builder = self::query_builder();
        return $builder->where('ask_id', $ask_id)->count();
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
        return $builder->where('uid', $uid) ->count();
    }

    public function get_replies($cond= array(), $page, $limit=0, $uid = 0)
    {
        $builder = self::query_builder();
        foreach ($cond as $k => $v){
            if($v){
                $builder = $builder->where($k, $v);
            }
        }

        // 过滤被删除到帖子
        $builder->select('replies.*');
        $builder->join('asks', 'replies.ask_id', '=', 'asks.id');
        $builder->where('asks.status', '>', self::STATUS_DELETED );

        return self::query_page($builder, $page, $limit);
    }

    public static function query_builder($alias = '')
    {
        $class = get_called_class();

        $builder = new $class;
        $table_name = 'replies';
        $builder = $builder ->lastUpdated()
            ->orderBy($table_name.'.create_time', 'DESC');

        //列表页面需要屏蔽别人的广告贴，展示自己的广告贴
        $builder->where(function($query)  {
            $uid = _uid();
            $query = $query->valid();
            //加上自己的广告贴
            if( $uid ) {
                $query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
            }
        });

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
}
