<?php
namespace App\Services;

use App\Models\Sms as mSms;

class Sms extends ServiceBase
{

    public static function addNewSms($phone, $data)
    {
        $sms = new mSms;
        $sms->add_new_sms($phone, $data);

        return $sms;
    }

    public static function updateSms($phone) {
        $sms = new mSms;
        $sms->update_sms($phone);

        return $sms;
    }

    public static function countMiss() {
        $sms = new mSms;
        return $sms->today_useless_sms_count();
    }

    public static function countTotal() {
        $sms = new mSms;
        return $sms->today_total_sms_count();
    }
}
