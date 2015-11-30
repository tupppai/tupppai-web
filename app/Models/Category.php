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

    public function get_categories(){
        return $this->leftjoin('categories as par_cat', 'categories.pid', '=', 'par_cat.id')
                    ->where( 'par_cat.status', '>', 0 )
                    ->where( 'categories.status', '>', 0 )
                    ->orderBy( 'par_cat.id', 'ASC' )
                    ->orderBy( 'categories.pid', 'ASC' )
                    ->orderBy( 'categories.id', 'ASC' )
                    ->select('categories.*')
                    ->get();
    }

    public function get_category_by_id($id) {
        return $this->find($id);
    }
    public function get_category_by_pid($pid) {
        return $this->where('pid', $pid )->get();
    }
}
