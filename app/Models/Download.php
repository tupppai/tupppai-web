<?php

namespace Psgod\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Download extends ModelBase
{

    /**
     * 求助的下载
     */
    const TYPE_ASK = 1;
    /**
     * 回复的下载
     */
    const TYPE_REPLY = 2;
    /**
     * 如果回复过求P 这里置为已完成
     */
    const STATUS_REPLIED = 1;
    /**
     * 初始化状态
     */
    const STATUS_NORMAL = 0;
    /**
     * 坑爹的初始化状态
     */
    const STATUS_DELETED = -1;

    public function getSource()
    {
        return 'downloads';
    }

    /**
    * 分页方法
    */ 
    public function page($keys = array(), $page, $limit)
    {
        $builder = self::query_builder();
        $conditions = 'TRUE';
        foreach ($keys as $k => $v) {
            $conditions .= " AND $k = :$k:";
        }

        $builder->where($conditions, $keys);
        $builder->andWhere('status = :status:', array('status' => self::STATUS_NORMAL));
        return self::query_page($builder, $page, $limit);
    }


    // ======================================
    //public static function addNewDownload($uid, $type, $target_id, $url, $status){
    //public static function get_download_target($type, $uid) {
    //public static function  get_current_ask($uid, $target_id){
    //public static function get_progressing($uid, $last_updated, $page=1, $limit=10) {
    //public static function get_inprogress($uid, $last_updated, $page, $limit){
}
