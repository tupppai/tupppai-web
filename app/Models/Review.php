<?php

namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Review extends ModelBase
{
    protected $table = 'reviews';

    public function beforeCreate()
    {
        $this->release_time= 0;
        $this->score       = 0;
        $this->evaluation  = '';
        $this->uid         = 0;
        // 马甲账号的id
        $this->puppet_uid  = 0;
        // 如果有作品，需要设置他的上级id
        $this->review_id   = 0;
        // 默认type为ask
        $this->type        = self::TYPE_ASK;
        $this->ask_id      = 0;
        $this->create_time = time();
        $this->update_time = time();
    }

    public function get_review_by_id($id) {
        return $this->where('id',$id)->first();
    }
}
