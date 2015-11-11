<?php  namespace App\Http\Controllers\Main;

use App\Models\Banner;
use App\Models\ActionLog;

use App\Services\Banner as sBanner;

class BannerController extends ControllerBase {

    public $_allow = array('index');

    // page index
    public function index() {
        $this->get('type', 'normal');
        $banners = sBanner::getBanners();
        return $this->output($banners);
    }
    
}
