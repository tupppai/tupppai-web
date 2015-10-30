<?php namespace App\Models;
use App\Models\ThreadCategory as mThreadCategory;
class Ask extends ModelBase
{
    protected $table = 'asks';
    const TYPE_NORMAL = 1;

    /**
     * 绑定映射关系
     */
    public function asker() {
        return $this->belongsTo('App\Models\User', 'uid');
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;
        $this->type         = self::TYPE_NORMAL;
        $this->ip           = get_client_ip();

        return $this;
    }

    /**
     * 通过uid数组获取用户的求助信息
     */
    public function get_asks_by_askids($askids, $page, $limit){
        $builder = self::query_builder();
        $builder = $builder->whereIn('id', $askids)
            ->orderBy('reply_count', 'DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 通过uid数组获取用户的求助信息
     */
    public function get_asks_by_uids($uids, $page, $limit){
        $builder = self::query_builder();
        $builder = $builder->whereIn('uid', $uids)
            ->orderBy('update_time', 'DESC')
            ->orderBy('reply_count', 'DESC');
        return self::query_page($builder, $page, $limit);
    }

    public function get_ask_ids_by_uid( $uid ){
        $builder = self::query_builder();
        return $builder->where('uid', $uid)
            ->lists('id');
    }

    /**
    * 获取首页数据
    */
    public function get_asks($keys = array(), $page=1, $limit=10 )
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            if($v) $builder = $builder->where($k, '=', $v);
        }
        return self::query_page($builder, $page, $limit);
    }

    public static function query_builder($alias = '')
    {
        $class = get_called_class();

        $builder = new $class;
        $table_name = $builder->getTable();
        $builder = $builder ->lastUpdated()
            ->orderBy($table_name.'.create_time', 'DESC');

        //列表页面需要屏蔽别人的广告贴，展示自己的广告贴
        $builder->where(function($query){
            $uid = _uid();
            //加上自己的广告贴
            if( $uid ) 
                $query = $query->valid() ->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
        });
        

        return $builder;
    }

    /**
     * 通过id获取求助
     */
    public function get_ask_by_id($ask_id) {
        return self::find($ask_id);
    }

    /**
     * 统计用户发布正常求助的数量
     */
    public function count_asks_by_uid($uid) {
        return self::query_builder()->where('uid', $uid)->count();
    }

    /**
     * umeng, 通过ask_ids 获取uid发布的ask的数量
     */
    public function list_user_ask_count($ask_ids) {
        $builder = self::query_builder();

        $builder = $builder->select('uid, count(1) as num')
            ->whereIn('id', $ask_ids)
            ->groupBy('uid');

        return self::query_page($builder);
    }

    public function change_asks_status( $uid, $to_status, $from_status = '' ){
        $cond = [
            'uid' => $uid
        ];
        if( !$from_status ){
            $cond['status']=$from_status;
        }
        return $this->where( $cond )->update(['status'=> $to_status]);
    }
}
