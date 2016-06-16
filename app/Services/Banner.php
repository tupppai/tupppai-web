<?php
namespace App\Services;

use App\Models\Banner as mBanner;

use App\Services\ActionLog as sActionLog;

class Banner extends ServiceBase{

    public static function addNewBanner( $uid, $desc, $small_pic, $large_pic, $url, $pc_url){
        $banner = new mBanner();
        sActionLog::init( 'ADD_BANNER', $banner);

        $banner->assign(array(
            'uid'       => $uid,
            'desc'      => $desc,
            'small_pic' => $small_pic,
            'large_pic' => $large_pic,
            'url'       => $url,
            'pc_url'       => $pc_url,
            'status'    => mBanner::STATUS_NORMAL
        ));
        $banner->save();
        sActionLog::save( $banner );

        return $banner;
    }

    public static function getBannerById($id) {
        return (new mBanner)->get_banner_by_id($id);
    }
    
    public static function getBanners(){
        $banner = new mBanner();
        $banners = $banner->get_banners();

        return $banners;
    }

    public static function delBanner( $uid, $banner_id ){
        $mBanner = new mBanner();
        $banner = $mBanner->get_banner_by_id($banner_id);
        if( !$banner )
            return error( 'BANNER_NOT_EXIST' );
        //sActionLog::init( 'DELETE_BANNER', $banner );

        $banner->status = mBanner::STATUS_DELETED;
        /*
        $banner->assign(array(
            'del_by'    => $uid,
            'del_time'  => time()
        ));
         */
        $banner->save();
        //sActionLog::save( $banner );

        //return self::brief( $banner );
        return $banner;
    }


    public static function sortBanners( $sorts ){
        $mBanner = new mBanner();
        foreach ($sorts as $order => $id) {
            $mBanner->set_order( $id, $order );
        }

        return true;
    }

    public static function detail( $banner ){
        $data = [];
        $data['id'] = $banner->id;
        $data['small_pic'] = $banner->small_pic;
        $data['large_pic'] = $banner->large_pic;
        $data['url'] = $banner->url;
        $data['pc_url'] = $banner->pc_url;
        $data['desc'] = $banner->desc;

        return $data;
    }

}
