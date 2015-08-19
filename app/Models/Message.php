<?php namespace App\Models;

class Message extends ModelBase
{
    protected $table = 'messages';

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

    public function get_messages_by_type( $uid, $type, $page, $size, $last_updated ){
        return $this->where([
                'receiver' => $uid,
                'msg_type' => $type,
                'status'=>self::STATUS_NORMAL
            ])
            ->where( 'update_time', '<', $last_updated )
            ->forPage( $page, $size )
            ->get();
    }
}
