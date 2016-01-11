<?php namespace App\Trades\Models;

use Illuminate\Database\Eloquent\Model,
    App\Traits\SoftDeletes;

class ModelBase extends Model {
    public $timestamps = true;

    public function beforeSave() {

    }

    /**
     * 保存
     */
    public function save(array $options = []) {
        $this->beforeSave();

        $result = parent::save($options);

        if($result == false){
            $str = "Save data error: " . implode(',', $this->getMessages());
            return error('SYSTEM_ERROR', $str);
        }

        return $this;
    }
}
