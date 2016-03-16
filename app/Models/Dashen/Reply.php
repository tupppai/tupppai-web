<?php

namespace App\Models\Dashen;

use App\Models\ModelBase;

class Reply extends ModelBase
{
    public $connection = 'db_ds';

    public function upload()
    {
        return $this->hasMany('App\Models\Dashen\Upload', 'id', 'upload_id');
    }
}
