<?php

namespace App\Services;

use \App\Models\Usermeta as mUsermeta;
use App\Services\ActionLog as sActionLog;

class Usermeta extends ServiceBase{

    public static function get( $uid, $key, $default_value = NULL, $is_int = false ){
        $meta = (new mUsermeta)->where([
            'uid' => $uid,
            'umeta_key' => $key
        ])->first();

        if( $meta ){
            return $is_int ? $meta->umeta_int_value : $meta->umeta_str_value;
        }
        else{
            return $default_value;
        }
    }


   //deprecated
    /**
     * 添加用户 key value 类型数据
     *
     * @param integer $uid   用户ID
     * @param string  $key   键
     * @param string  $value 值
     * @param boolean $is_int值是否是数字
     */
    public static function writeUserMeta($uid, $key, $value, $is_int=false)
    {
    	$meta = new mUsermeta();
        $cond = [
            'uid' => $uid,
            'umeta_key' => $key
        ];
        $umeta = $meta->firstOrNew( $cond );
        if( !$umeta->umeta_id ){
            $umeta->uid = $uid;
            $umeta->umeta_key = $key;
        }
        if ($is_int) {
            $umeta->umeta_int_value = $value;
        } else {
            $umeta->umeta_str_value = $value;
        }
        return $umeta->save();
    }

    /**
     * 获取用户相关数据
     *
     * @param string $uid 用户ID
     * @param string $key 可选。键
     * @param string $is_int 可选。值是否为整型
     */
    public static function readUserMeta($uid, $key='', $is_int=false)
    {
    	$umeta = new mUsermeta();
        if (!empty($key)) { // 有指定键，就只找出这个键的值
            $result = $umeta->where([
                'uid' => $uid,
                'umeta_key' => $key
            ])->first();
            if ($result) {
                return array(
                    $key => $is_int ? (int) $result->umeta_int_value : $result->umeta_str_value
                );
            } else {
                return array();
            }
        } else {    // 没指定键就去找这个用户所有的值
            $result = array();
            $metas = $umeta->where('uid',$uid)->first();
            if ($metas) {
                foreach ($metas as $m) {
                    $result[$m->key] = ( !empty($m->umeta_int_value)||($m->umeta_int_value===0) ? $m->umeta_int_value : $m->umeta_str_value);
                }
            }

            return $result;
        }
    }


    //todo::refactor
    /**
     * 添加用户备注
     * @param  [int]    $uid    [用户id]
     * @param  [string] $remark [用户备注]
     * @return [model]  $umeta  [用户扩展模型]
     */
    public static function write_user_remark($uid, $remark) {
        return self::writeUserMeta($uid, mUsermeta::KEY_REMARK, $remark, false);
    }

    /**
     * 获取用户备注
     * @param  [int]    $uid    [用户id]
     * @return [string] $remark [用户备注]
     */
    public static function read_user_remark($uid) {
        $result = self::readUserMeta($uid, mUsermeta::KEY_REMARK, false);
        if($result)
            return $result[mUsermeta::KEY_REMARK];
        else
            return '';
    }

    /**
     * [read_user_forbid 获取用户禁言状态]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public static function read_user_forbid($uid){
        return self::get($uid, mUsermeta::KEY_FORBID, '',  true);
    }

    /**
     * 添加用户禁言状态
     * @param  [int]    $uid    [用户id]
     * @param  [string] $value  [禁言值(-1永久禁言,0或者过去的时间为不禁言,将来的时间则为禁言)]
     */
    public static function write_user_forbid($uid, $value) {
        return self::writeUserMeta($uid, mUsermeta::KEY_FORBID, $value, true);
    }

    public static function refresh_read_notify( $uid, $type, $time = -1 ){
        $last_modified = self::readUserMeta( $uid, $type );
        if( !array_key_exists($type, $last_modified ) ){
            $last_modified = 0;
        }
        else{
            $last_modified = $last_modified[$type];
        }
        if( $time == -1 ){
            $time = time();
        }
        self::writeUserMeta( $uid, $type, $time );

        return $last_modified;
    }

    public static function getUserRate( $uid ){
        $rate = self::get();
        get_money($row->current_time, $row->rate, 'hour');
    }

}
