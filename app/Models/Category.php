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

    public function get_categories( $type = 'all' ){
        $query = $this->leftjoin('categories as par_cat', 'categories.pid', '=', 'par_cat.id')
                    ->where( 'par_cat.status', '>', 0 )
                    ->where( 'categories.status', '>', 0 )
                    ->orderBy( 'categories.pid', 'ASC' )
                    ->orderBy( 'categories.id', 'DESC' )
                    ->select('categories.*');
        switch( $type ){
            case 'channels':
                $query = $query->where( 'categories.pid', self::CATEGORY_TYPE_CHANNEL );
                break;
            case 'activities':
                $query = $query->where( 'categories.pid', self::CATEGORY_TYPE_ACTIVITY );
                break;
            case 'all':
                $query = $query->whereIn( 'categories.pid', [
                    self::CATEGORY_TYPE_CHANNEL,
                    self::CATEGORY_TYPE_ACTIVITY
                ] );
            default:
                break;
        }
        return $query->get();
    }

    public function get_category_by_id($id) {
        return $this->find($id);
    }
    public function get_category_by_pid($pid, $status = '') {
        return $this->where('pid', $pid )
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
                    })
                    ->get();
    }

    public function find_category_by_cond( $cond ){
        return $this->where( function( $query) use ( $cond ){
                        $status = $cond['status'];
                        $display_name = $cond['display_name'];
                        $pid = $cond['pid'];

                        if( !is_array( $status ) ){
                            $status = [$status];
                        }
                        if( $status ){
                            $query->whereIn( 'status', $status );
                        }

                        if( $display_name ){
                            $query->where( 'display_name', 'LIKE', $display_name.'%' );
                        }

                        if( !is_null($pid) ){
                            $query->where('pid', $pid);
                        }

                    })
                    ->get();
    }
    public function get_categories_by_name( $name, $status = '' ){
        $cond = [];
        $cond['display_name'] = $name;
        return $this->find_category_by_cond( $cond );
    }
}
