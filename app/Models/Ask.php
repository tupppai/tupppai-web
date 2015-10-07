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

    /*
    public function upload() {
        return $this->hasOne('App\Models\Upload', 'id', 'upload_id');
    }
     */

    // status scope
    public function scopeValid( $query ){
        return $query->where( 'status', '>', 0 );
    }
    public function scopeInvalid( $query ){
        return $query->where( 'status', '<', 0 );
    }
    public function scopeDeleted( $query ){
        return $query->where( 'status', 0 );
    }

    public function scopeNormal( $query ){
        return $query->where( 'status', self::STATUS_NORMAL );
    }

    public function scopeBanned( $query ){
        return $query->where( 'status', self::STATUS_BANNED );
    }
    public function scoepAllowed( $query ){
        return $query->where( 'status', '!=', self::STATUS_BANNED );
    }
    public function scopeFilterBanned( $query, $uid ){
        return $query->allowed()->where(['uid' => $uid, 'status' => self::STATUS_BANNED] );
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
     * umeng, 通过ask_ids 获取uid发布的ask的数量
     */
    public function list_user_ask_count($ask_ids) {

        $builder = self::query_builder();

        $builder = $builder->select('uid, count(1) as num')
            ->where('status', self::STATUS_NORMAL)
            ->whereIn('id', $ask_ids)
            ->groupBy('uid');

        return self::query_page($builder);
    }

    /**
     * 更新ask点击次数
     * @return [boolean]
     */
    public function increase_click_count(){
        return self::find($this->id)
            ->increment('click_count', 1);
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
        return $this->where( [
                'uid' => $uid,
                'status' => self::STATUS_NORMAL
            ] )
            ->lists( 'id' );
    }

    public function get_asks_by_uid( $uid, $page, $limit, $last_read_time = NULL ){
        $builder = self::query_builder();

        if( !is_null( $last_read_time) ){
            $last_read_time = time();
        }

        $builder = $builder->where('uid', $uid)
            ->where('update_time','<', $last_read_time )
            ->orderBy('update_time', 'DESC');

        return self::query_page($builder, $page, $limit);
    }

    /**
    * 分页方法
    *
    * @param int 加数
    * @param int 被加数
    * @return integer
    */
    public function page($keys = array(), $page=1, $limit=10, $type=null, $uid = 0 )
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, '=', $v);
        }

        if($type == 'new'){
            $builder = $builder->where('reply_count', 0);
            $builder = $builder->orderBy('update_time', 'DESC');
        } else if($type == 'hot'){
            $builder = $builder->where('reply_count', '>', 0);
            $builder = $builder->orderBy('update_time', 'DESC');
            $builder = $builder->orderBy('reply_count', 'DESC');
        }
        $builder = $builder->where('status','>', 0 )
                           ->where('status','!=', self::STATUS_BLOCKED ); //排除别人的广告贴
        if( $uid ){
            $builder = $builder->orWhere([ 'uid'=>$uid, 'status'=> self::STATUS_BLOCKED ]); //加上自己的广告贴
        }

        $asks = $builder->forPage( $page, $limit )->get();

        return $asks;
    }

    /**
     * 统计总数
     */
    public function sum($keys = array(), $type=null)
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, '=', $v);
        }

        if($type == 'new'){
            $builder = $builder->where('reply_count', 0);
            $builder = $builder->orderBy('update_time', 'DESC');
        } else if($type == 'hot'){
            $builder = $builder->where('reply_count', '>', 0);
            $builder = $builder->orderBy('update_time', 'DESC');
            $builder = $builder->orderBy('reply_count', 'DESC');
        }

        return self::query_sum($builder);
    }

    /**
     * 通过id获取求助
     */
    public function get_ask_by_id($ask_id) {
        $ask   = self::find($ask_id);

        return $ask;
    }

    public function count_asks_by_uid($uid) {
        $count = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }
}
