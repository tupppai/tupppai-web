<?php namespace App\Models;

class Banner extends ModelBase {

    protected $table = 'banners';
    
    
    public function get_banners(){
        // todo: configurize
        //define( 'APP_BANNER_LIST_NUM', 4 );
        $apps = $this
            ->where('status', self::STATUS_NORMAL)
            //->orderBy('update_time', 'desc')
            //->forPage(0, APP_BANNER_LIST_NUM)
            ->get();
           
        return $apps;
    }

    public function get_banner_by_id($id) {
        return self::find($id);
    }

    public function set_order( $id, $order ){
        return $this->where( 'id', $id )->update(['orderBy'=>$order]);
    }
}
