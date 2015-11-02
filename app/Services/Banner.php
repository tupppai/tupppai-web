<?php
namespace App\Services;

use App\Models\Banner as mBanner;

use App\Services\ActionLog as sActionLog;

class Banner extends ServiceBase{

    public static function addNewBanner( $uid, $desc, $small_pic, $large_pic, $url){
        $banner = new mBanner();
        sActionLog::init( 'ADD_BANNER', $banner);

        $banner->assign(array(
            'uid'       => $uid,
            'desc'      => $desc,
            'small_pic' => $small_pic,
            'large_pic' => $large_pic,
            'url'       => $url
        ));
        $banner->save();
        sActionLog::save( $banner );

        return $banner;
    }
    
    
    public static function getBanners(){
        $banner = new mBanner();
        $banners = $banner->get_banners();

        return $banners;
    }

}
