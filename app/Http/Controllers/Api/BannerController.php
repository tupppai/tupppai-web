<?php namespace App\Http\Controllers\Api;

use App\Services\Banner as sBanner;

class BannerController extends ControllerBase{

    public $_allow = '*';

    public function get_banner_listAction(){
        $banners = sBanner::getBanners();
        return $this->output($banners);
    }
}
