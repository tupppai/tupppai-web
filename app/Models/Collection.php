<?php

namespace App\Models;

class Collection extends ModelBase
{
    /**
     * 取消的收藏
     */
    const STATUS_CANCEL = 0;

    /**
     * 正常的收藏
     */
    const STATUS_NORMAL = 1;


    public function getSource()
    {
        return 'collections';
    }


    /**弃用
     * [collection 收藏/取消收藏 回复]
     * @param  [type] $uid [用户ID]
     * @param  [type] $rid [回复ID]
     * @return [type]      [description]
     */
    //TODO remove
    public static function collection($uid, $rid, $status){
        $collection = new self();
        $collection->uid = $uid;
        $collection->reply_id = $rid;
        $collection->create_time = time();
        $collection->update_time = time();
        $collection->status = $status;

        return $collection->save_and_return($collection);
    }

    public static function setCollection($uid, $rid, $status)
    {
        $collection = self::findFirst(array(
            "uid = '$uid' AND reply_id = '$rid'"
        ));
        if($collection) {
            if($collection->status==$status) {
                return true;
            }
        }
        else {
            $collection = new self();
            $collection->uid  = $uid;
            $collection->reply_id = $rid;
            $collection->create_time = time();
        }
        $collection->status = $status;
        $collection->update_time = time();
        return $collection->save_and_return($collection);
    }

    /**
     * [get_user_collection 获取用户收藏]
     * @param  [type] $uid   [description]
     * @param  [type] $page  [description]
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
        

    public static function checkUserReplyCollection( $uid, $target_id ){
        $builder = Collection::query_builder();
        $res = $builder ->where('uid='.$uid.' AND status='.Collection::STATUS_NORMAL.' AND reply_id='.$target_id)
                        ->columns('count(*) as c ')
                        ->getQuery()
                        ->execute();

        if( $res->toArray()[0]['c'] ){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * 计算用户收藏作品数量
     */
    public function count_user_collection($uid) {
        $count = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 是否收藏
     */
    public function has_collected_reply($uid, $reply_id) {
        $collection = self::where('reply_id', $reply_id)
            ->where('status', self::STATUS_NORMAL)
            ->where('uid', $uid)
            ->first();
        
        return $collection; 
    }

    /**
     * 获取用户收藏的
     public static function get_user_collection($uid, $page, $limit){
         */

}
