<?php
namespace App\Models;

class Config extends ModelBase
{

    protected $table = 'configs';

    public function get_config($key){
        return $this->where('name', $key)->first();
    }

    public function set_config( $value, $remark = '' ){
        $this->value = $value;
        $this->remark = $remark;
        $this->save();
        return $this;
    }
}
