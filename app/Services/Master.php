<?php
namespace Psgod\Services;

use Psgod\Models\User as mUser,
    Psgod\Models\Master as mMaster;

class Master extends ServiceBase{

    public static function updateMasters(){
        $master = new mMaster();
        $phql = 'UPDATE '.$master->getSource().' SET status='.mMaster::STATUS_VALID.' WHERE start_time<UNIX_TIMESTAMP() AND end_time>UNIX_TIMESTAMP() AND status='.mMaster::STATUS_PENDING;
        return $master->getReadConnection()->query($phql);
    }

    public static function getMasterList($page = 1, $size = 15){
        self::updateMasters();
        $mMaster = new mMaster;

        $masters = $master->page(array(), $page, $size);

        $uids = array();
        foreach($masters as $master){
            $uids[] = $master->uid;
        }

        $users = mUser::get_user_by_uids($uids, $page, $size);
        return $users;
    }
}
