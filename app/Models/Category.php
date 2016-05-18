<?php namespace App\Models;

class Category extends ModelBase{
    protected $table = 'categories';
    protected $guarded = ['id'];

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;
        $this->create_by    = 0;
        $this->update_by    = 0;

        return $this;
    }

    public function get_categories( $type = 'all', $status, $page = 0 , $size = 0  ){
        $query = $this->leftjoin('categories as par_cat', 'categories.pid', '=', 'par_cat.id')
                    ->where( 'par_cat.status', '>', 0 )
                    ->wherein( 'categories.status', $status )
                    ->orderBy('order', 'ASC')
                    ->select('categories.*');
        switch( $type ){
            case 'channels':
                $query = $query->where( 'categories.pid', self::CATEGORY_TYPE_CHANNEL );
                break;
            case 'activities':
                $query = $query->where( 'categories.pid', self::CATEGORY_TYPE_ACTIVITY );
                break;
            case 'tutorials':
                $query = $query->where( 'categories.pid', self::CATEGORY_TYPE_TUTORIAL );
                break;
            case 'wx_activities':
                $query = $query->where( 'categories.pid', self::CATEGORY_TYPE_WX_ACTIVITY );
                break;
            case 'home':
                $query = $query->whereIn( 'categories.pid', [
                    self::CATEGORY_TYPE_CHANNEL,
                    self::CATEGORY_TYPE_ACTIVITY
                ]);
                break;
            case 'all':
                $query = $query->where(function( $q ){
                    $q = $q->whereIn( 'categories.pid', [
                        self::CATEGORY_TYPE_CHANNEL,
                        self::CATEGORY_TYPE_ACTIVITY
                    ] );
                    $q = $q->orWhere('categories.id',self::CATEGORY_TYPE_TUTORIAL);
                });
            default:
                break;
        }
        if( $page && $size ){
            $query = $query->forPage( $page, $size );
        }
        return $query->get();
    }

    public function get_category_by_id($id) {
        return $this->find($id);
    }
    public function get_category_by_pid($pid, $status = '', $page = 0, $size = 0) {
        $query = $this->where('pid', $pid )
                    ->where( function( $query ) use ( $status ){
                        if( is_int( $status ) ){
                            $status = [ $status ];
                        }
                        if( is_string( $status ) ){
                            $status = explode(',', $status );
                        }

                        if( $status ){
                            $query->wherein( 'status', $status );
                        }
                    });
        if( $page && $size ){
            $query->forPage( $page, $size );
        }
        return $query->get();
    }

    public function find_category_by_cond( $cond ){
        $status = NULL;
        if( isset( $cond['status'] ) ){
            $status = $cond['status'];
            if( !is_array( $status ) ){
                $status = [$status];
            }
        }

        $name = '';
        if( isset( $cond['name'] ) ){
            $name = $cond['name'];
        }

        $display_name = '';
        if( isset($cond['display_name']) ){
            $display_name = $cond['display_name'];
        }

        $pid = NULL;
        if( isset( $cond['pid'] ) ){
            $pid = $cond['pid'];
            if( !is_array( $pid ) ){
                $pid = [$pid];
            }
        }
        return $this->where( function( $query) use ( $status, $name, $display_name, $pid ){
                        if( !is_null( $pid ) ){
                            $query->whereIn( 'pid', $pid );
                        }

                        if( !is_null( $status ) ){
                            $query->whereIn( 'status', $status );
                        }

                        if( $display_name ){
                            $query->where( 'display_name', 'LIKE', '%'.$display_name.'%' );
                        }
                        if( $name ){
                            $query->where( 'name', 'LIKE', '%'.$name.'%' );
                        }
                    })
                    ->get();
    }
    public function get_categories_by_display_name( $display_name, $status = '' ){
        $cond = [];
        $cond['display_name'] = $display_name;
        return $this->find_category_by_cond( $cond );
    }
    public function get_category_by_name( $name, $status = '' ){
        $cond = [];
        $cond['name'] = $name;
        $categories = $this->find_category_by_cond( $cond );
        if( $categories->isEmpty() ){
            return false;
        }
        else{
            return $categories[0];
        }
    }

    public function getCategoryKeywordHasActivityChannelList($q)
    {
        if($q != 'all'){
            $catgorys = $this->where('display_name','LIKE','%'.$q.'%');
        }
        else{
            $catgorys = $this;
        }
        $catgorys = $catgorys->whereIn('pid',[self::CATEGORY_TYPE_ACTIVITY,self::CATEGORY_TYPE_CHANNEL])->get();
        return $catgorys;
    }

    public function set_order( $id, $order ){
        return $this->where( 'id', $id )->update(['order'=>$order]);
    }
}
