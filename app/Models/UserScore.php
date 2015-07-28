<?php
namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class UserScore extends ModelBase
{
    //这个status是个坑，后面估计也同步不了了
    const STATUS_NORMAL = 0;
    const STATUS_PAID   = 1;
    const STATUS_COMPLAIN = 2;
    const STATUS_DELETED  = 3;

    const TYPE_ASK      = 1;
    const TYPE_REPLY    = 2;

    public function getSource()
    {
        return 'user_scores';
    }
}
