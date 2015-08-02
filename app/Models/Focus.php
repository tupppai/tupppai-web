<?php

namespace App\Models;

class Focus extends ModelBase
{
    protected $table = 'focuses';

    /**
     * 取消的关注
     */
    const STATUS_CANCEL = 0;

    /**
     * 正常的关注
     */
    const STATUS_NORMAL = 1;

    /**弃用
     * [focus 关注/取消关注 问题]
     * @param  [type] $uid [用户ID]
     * @param  [type] $aid [作品ID]
     * @return [type]      [description]
     */
    //TODO remove function focus
    public static function focus($uid, $aid, $status){
        $focus = new self();

        $focus->uid = $uid;
        $focus->ask_id = $aid;
        $focus->status = $status;

        return $focus->save();
    }


    public static function setFocus($uid, $aid, $status)
    {
        $focus = self::findFirst(array(
            "uid = '$uid' AND ask_id = '$aid'"
        ));
        if($focus) {
            if($focus->status==$status) {
                return $focus;
            }
            $focus->status = $status;
            $focus->update_time = time();
        }
        else {
            $focus = new self();
            $focus->uid  = $uid;
            $focus->ask_id = $aid;
            $focus->status = $status;
            $focus->create_time = time();
            $focus->update_time = time();
        }
        return $focus->save_and_return($focus);
    }

    //public static function checkUserAskFocus( $target_id, $uid = 0){

    public function get_user_focus_asks($uid) {
        $focuses = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->get();

        return $focuses;
    }

    public function get_user_focus_ask($uid, $ask_id) {
        $mFocus = self::where('uid', $uid)
            ->where('ask_id', $ask_id)
            ->where('status', self::STATUS_NORMAL)
            ->first();
            
        return $mFocus;
    }

    public function has_focused_ask($uid, $ask_id) {
         $mDownload = self::where('uid', $uid)
             ->where('ask_id', $ask_id)
             ->where('status', self::STATUS_NORMAL)
             ->first();
            
         return $mDownload;
    }
}
