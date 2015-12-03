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

    public function get_categories( $type ){
        $query = $this->leftjoin('categories as par_cat', 'categories.pid', '=', 'par_cat.id')
                    ->where( 'par_cat.status', '>', 0 )
                    ->where( 'categories.status', '>', 0 )
                    ->orderBy( 'par_cat.id', 'ASC' )
                    ->orderBy( 'categories.pid', 'ASC' )
                    ->orderBy( 'categories.id', 'ASC' )
                    ->select('categories.*');
        switch( $type ){
            case 'channels':
                $query = $query->where('categories.id', '>', self::CATEGORY_TYPE_ACTIVITY )
                                ->where( 'categories.pid', '!=', self::CATEGORY_TYPE_ACTIVITY);
                break;
            case 'all':
            default:
                break;
        }
        return $query->orderBy('categories.id', 'DESC')
                ->get();
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
}
