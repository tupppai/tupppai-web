<?php

namespace App\Models;

class Focus extends ModelBase
{
    protected $table = 'focuses';
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
         $mFocus = self::where('uid', $uid)
             ->where('ask_id', $ask_id)
             ->where('status', self::STATUS_NORMAL)
             ->first();
            
         return $mFocus;
    }

    public function get_focuses_by_askid($ask_id) {
        $focuses = self::where('ask_id', $ask_id)
            ->where('status', self::STATUS_NORMAL)
            ->get();
        
        return $focuses;
    }
}
