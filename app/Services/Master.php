<?php
namespace App\Services;

use App\Models\User as mUser,
    App\Models\Master as mMaster;

class Master extends ServiceBase{
    public static function updateMasters(){
        $mMaster = new mMaster();

        $mMaster->where( 'start_time', '<', time() )
                ->where( 'end_time', '>', time() )
                ->where( 'status', mMaster::STATUS_PENDING )
                ->update( [ 'status' => mMaster::STATUS_VALID ] );

        return true;
    }

    public static function getAvailableMasters( $page = 1, $size = 15 ){
        self::updateMasters();
        $mMaster = new mMaster;

        $masters = $mMaster->validMasters()
                           ->forPage( $page, $size )
                           ->lists( 'uid' );
        return $masters;
    }
}
