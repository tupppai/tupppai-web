<?php namespace App\Models;
use App\Models\ThreadCategory as mThreadCategory;
class Ask extends ModelBase
{
    protected $table = 'asks';
    const TYPE_NORMAL = 1;

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 1000;
    }

    public function getAmountAttribute($value)
    {
        return $value / 1000;
    }
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
        $builder = $builder->whereIn('id', $askids);
        $builder = $builder->orderBy('create_time','DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 通过uid数组获取用户的求助信息 v2
     */
    public function get_asks_by_askids_v2($askids, $page, $limit){
        $builder = self::query_builder();
        $builder = $builder->whereIn('id', $askids);
        $builder = $builder->orderBy('create_time','DESC');
        return self::query_page($builder, $page, $limit);
    }
    /**
     * 通过uid数组获取用户的求助信息
     */
    public function get_asks_by_uids($uids, $page, $limit){
        $builder = self::query_builder();
        $builder = $builder->whereIn('uid', $uids)
            ->orderBy('create_time', 'DESC');
        return self::query_page($builder, $page, $limit);
    }

    public function get_ask_ids_by_uid( $uid, $page = null, $size = null ){
        $builder = self::query_builder();
        $builder = $builder->where('uid', $uid);
        if( !$size ){
            return $builder->lists('id');
        }
        else{
            return $builder->forPage( $page, $size )->lists( 'id' );
        }
    }

    public function get_hidden_ask_by_category_id($category_id) {

        $ask = $this->where('status', '=', self::STATUS_HIDDEN);

        //获取ask_id
        $target_ids = $this->from('thread_categories')
                ->select('target_id')
                ->where('status', '>', self::STATUS_DELETED)
                ->where('target_type', self::TYPE_ASK)
                ->where('category_id', $category_id)->get();
        $ask_table  = $this->getTable();
        $ask        = $ask->whereIn("$ask_table.id", $target_ids);
        $ask        = $ask->orderBy('create_time','DESC')->first();

        return $ask;
    }

    public function get_completed_asks_by_category_id($category_id, $page, $size) {
        $ask_table  = $this->getTable();
        $builder    = self::query_builder();

        //获取ask_id
        $ids = $this->from('thread_categories')
            ->select('target_id')
            ->where('status', '>', self::STATUS_DELETED)
            ->where('target_type', self::TYPE_ASK)
            ->where('category_id', $category_id)
            ->get();

        $builder = $builder->whereIn("$ask_table.id",$ids)
                ->orderBy('last_reply_time', 'desc');

        return self::query_page($builder, $page, $size);
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
        //屏蔽用户
        //$builder = $builder->blockingUser(_uid());
        $builder = $builder->orderBy('create_time','DESC');
        return self::query_page($builder, $page, $limit);
    }

    public static function query_builder($alias = '')
    {
        $class = get_called_class();

        $builder = new $class;
        $table_name = $builder->getTable();
        $builder = $builder ->lastUpdated();

        //列表页面需要屏蔽别人的广告贴，展示自己的广告贴
        $builder->where(function($query){
            $uid = _uid();
            $query = $query->valid();
            //加上自己的广告贴
            if( $uid ){
                $query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
            }
        });


        return $builder;
    }

    /**
     * 通过id获取求助
     */
    public function get_ask_by_id($ask_id) {
        return self::find($ask_id);
    }

    public function get_ask_by_upload_ids($upload_ids){
        return self::where('upload_ids', $upload_ids)->first();
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

    public function sum_clicks_by_ask_ids( $askIds ){
        return $this->whereIn('id', $askIds)
                    ->where('status', '>=', self::STATUS_DELETED)
                    ->sum('click_count');
    }

    public function count_asks_by_uid( $uid ){
        return $this->where('uid', $uid)
                    ->valid()
                    ->count();
    }

    public function count_user_by_ask_ids( $ask_ids ){
        return $this->whereIn('id', $ask_ids )
                    ->groupBy('uid')
                    ->count();
    }
}
