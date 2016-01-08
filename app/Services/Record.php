<?php

namespace App\Services;

use \App\Models\Record as mRecord;

class Record extends ServiceBase
{
    public static function addRecord($uid, $target_id, $type, $action, $status) {
    	$rec = new self();
    	$rec->uid = $uid;
    	$rec->target_id = $target_id;
    	$rec->type = $type;
    	$rec->action = $action;
    	$rec->create_time = time();
    	$rec->status = $status;
    	return $rec->save_and_return($rec);
    }

    public static function updateRecord($uid, $target_id, $type, $action, $status) {
        return self::addRecord($uid, $target_id, $type, $action, $status);
    }

    public static function up($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_UP, $status);
    }


    public static function like($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_LIKE, $status);
    }


    public static function collect($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_COLLECT, $status);
    }


    public static function inform($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_INFORM, $status);
    }


    public static function share($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_SHARE, $status);
    }

    public static function wxshare($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_WEIXIN_SHARE, $status);
    }

    public static function comment($uid, $target_id, $type, $status=self::STATUS_NORMAL){
        return mRecord::updateRecord($uid, $target_id, $type, mRecord::ACTION_COMMENT, $status);
    }
}
