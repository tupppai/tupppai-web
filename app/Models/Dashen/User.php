<?php

namespace App\Models\Dashen;

use App\Models\ModelBase;

class User extends ModelBase
{
    public $connection = 'db_ds';

    public function Replies()
    {
        return $this->hasMany('App\Models\Dashen\Reply','uid','uid');
    }

    public function Asks()
    {
        return $this->hasMany('App\Models\Dashen\Ask','uid','uid');
    }

//    public function Comment()
//    {
//        return $this->hasMany('App\Models\Dashen\Comment','uid','uid');
//    }
}
