<?php namespace App\Models;

class Banner extends ModelBase {

    protected $table = 'banners';
    
    
    public function get_banners(){
        // todo: configurize
        define( 'APP_BANNER_LIST_NUM', 4 );
        $apps = $this
            //$this->where('status', self::STATUS_NORMAL)
            //->orderBy('update_time', 'desc')
            ->forPage(0, APP_BANNER_LIST_NUM)
            ->get();
           
        return $apps;
    }
}
