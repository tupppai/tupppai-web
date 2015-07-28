<?php

namespace Psgod\Models;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Message extends ModelBase
{

    const TYPE_COMMENT = 1; // 评论
    const TYPE_REPLY   = 2; // 作品
    const TYPE_FOLLOW  = 3; // 关注
    const TYPE_INVITE  = 4; // 邀请
    const TYPE_SYSTEM  = 5; // 系统

    const TARGET_ASK     = 1;
    const TARGET_REPLY   = 2;
    const TARGET_COMMENT = 3;
    const TARGET_USER    = 4;
    const TARGET_SYSTEM  = 5;

    public function getSource()
    {
        return 'messages';
    }
}
