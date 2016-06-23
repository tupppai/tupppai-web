<?php namespace App\Models;

class Tag extends ModelBase{
    protected $table = 'tags';
    protected $guarded = ['id'];

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function new_tag( $name, $uid = 0 ){
        $this->assign(array(
            'uid' => $uid,
            'name'=>$name
        ));

        return $this->save();
    }
    public function check_has_created_tag( $tagname ){
        return $this->where('name', $tagname )
                    ->exists();
    }
    public function check_tag_name_valid( $tagname ){
        return $this->where( 'name', $tagname )
                    ->where( 'status', '>=', self::STATUS_NORMAL)
                    ->exists();
    }

    public function get_tags($page, $size, $cond = []){
        return $this->where($cond)
                    ->forPage( $page, $size )
                    ->get();
    }

    public function get_tag_by_id($id) {
        return $this->find($id);
    }

    public function get_tag_by_name( $name ){
        return $this->where('name', $name)
                    ->first();
    }
    public function online_tag( $id ){
        return $this->where('id', $id)
                    ->update(['status' => self::STATUS_NORMAL]);
    }
}
