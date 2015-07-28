<?php

namespace App\Models;

use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    \App\Models\User;

class Ask extends ModelBase
{
    const TYPE_NORMAL = 1;
    public function getSource()
    {
        return 'asks';
    }

    /**
     * 绑定映射关系
     */
    public function initialize() {
        parent::initialize();

        $this->belongsTo('uid', 'App\Models\User', 'uid', array(
            'alias' => 'asker',
        ));
        $this->hasOne('upload_id', 'App\Models\Upload', 'id', array(
            'alias' => 'upload',
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
        $this->type         = self::TYPE_NORMAL;
        $this->ip           = get_client_ip();

        return $this;
    }

    /**
     * 通过uid数组获取用户的求助信息
     */
    public function get_asks_by_askids($askids, $page, $limit){
        $builder = self::query_builder();
        $builder->inWhere('id', $askids);
        $builder->orderBy('update_time DESC, reply_count DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * umeng, 通过ask_ids 获取uid发布的ask的数量
     */
    public function list_user_ask_count($ask_ids) {

        $builder = self::query_builder();

        $builder->columns('uid, count(1) as num')
            ->inWhere('id', $ask_ids)
            ->andWhere('status = :status:', array('status' => self::STATUS_NORMAL))
            ->groupBy('uid');

        return self::query_page($builder);
    }

    /**
     * 更新ask点击次数
     * @return [boolean]
     */
    public function increase_click_count(){
        $sql = 'UPDATE asks '.
            ' SET click_count = click_count + 1 '.
            ' WHERE id = ' . $this->id;

        $ask = new self();
        return $ask->getReadConnection()->query($sql);
    }

    /**
     * 通过uid数组获取用户的求助信息
     */
    public function get_asks_by_uids($uids, $page, $limit){
        $builder = self::query_builder();
        $builder->inWhere('uid', $uids);
        $builder->orderBy('update_time DESC, reply_count DESC');
        return self::query_page($builder, $page, $limit);
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
        $conditions = 'TRUE';
        foreach ($keys as $k => $v) {
            $conditions .= " AND $k = :$k:";
        }

        if($type == 'new'){
            $conditions .= " AND reply_count = 0";
            $builder->orderBy('update_time DESC');
        } else if($type == 'hot'){
            $conditions .= " AND reply_count > 0";
            $builder->orderBy('update_time DESC, reply_count DESC');
        }

        $builder->where($conditions, $keys);
        $builder->andWhere('status = :status:', array('status' => self::STATUS_NORMAL));
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 通过id获取求助
     */
    public function get_ask_by_id($ask_id) {
        $ask   = $this->findFirst($ask_id);

        return $ask;
    }


    // ================= waiting to delete
    //public static function addNewAsk($uid, $desc, $upload_obj)
    //public static function fellow_asks_page($uid, $page=1, $limit=10)
    //public static function set_reply_count($ask_id, $count=1){
    //public function get_replyers_array() {
    //public function get_comments() {
    //public function get_psgod($num = 5){
    //public function getHotCommentRows($limit=5) {
    //public function be_downloaded_by($uid) {
    //public static function focus_page($uid, $page = 1, $limit = 15)
    //public function get_labels_array()
    //public function toSimpleArray() {
    //public function to_simple_array() {
    //public function toStandardArray( $uid = 0, $width = 480 ) {
    //public function to_simple_array() {
}
