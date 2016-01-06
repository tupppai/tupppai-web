<?php namespace App\Models;

use DB;

class Sms extends ModelBase {

    //系统协助控制的时间
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $timestamps = false;

    protected $table = 'sms';

    public function add_new_sms($phone, $data) {
        $time = time();
        $date = date("Y-m-d H:i:s");

        $this->to   = $phone;
        $this->data = $data;
        $this->sent_time = $time;
        $this->created_at= $date;
        $this->updated_at= $date;
        $this->deleted_at= $date;

        return $this->save();
    }

    public function update_sms($phone) {
        $sms = $this->where('to', $phone)
            ->orderBy('id', 'desc')
            ->first();

        if($sms) {
            $sms->updated_at = date("Y-m-d H:i:s");
            $sms->save();
        }
        return true;
    }

    public function today_useless_sms_count() {
        return $this->where('updated_at', DB::raw('created_at'))
            ->where('sent_time', '>', strtotime(date("Ymd")))
            ->count();
    }
    
    public function today_total_sms_count() {
        return $this->where('sent_time', '>', strtotime(date("Ymd")))
            ->count();
    }
}
