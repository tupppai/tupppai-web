<?php

namespace App\Services;

use \App\Models\Replymeta as mReplymeta;

class Replymeta extends ServiceBase
{
    /**
     * 读取meta值
     */
    public static function readMeta($fid, $key){
        $mReplymeta = new mReplymeta;
        return $mReplymeta->read_meta($fid, $key);
    }

    /**
     * 写meta值
     */
    public static function writeMeta($fid, $key, $value)
    {
        $mReplymeta = new mReplymeta;
        return $mReplymeta->write_meta($fid, $key, $value);
    }
}
