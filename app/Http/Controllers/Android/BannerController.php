<?php namespace App\Http\Controllers\Android;

use App\Services\Banner as sBanner;

class BannerController extends ControllerBase{

    public $_allow = array(
        'get_banner_list',
    );

    public function get_banner_listAction(){
        $banners = sBanner::getBanners();
        return $this->output($banners);
    }
}
