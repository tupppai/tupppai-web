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

    public function comment(){
        return $this->belongsTo('\App\Models\Comment','target_id');
    }

    public function scopeOwn( $query, $uid ){
        $query->where('receiver', $uid);
    }

    public function scopeTypeOf( $query, $type ){
        $query->where('msg_type', $type);
    }

    public function scopeValid( $query ){
        $query->where('status', self::STATUS_NORMAL);
    }

    public function get_comment_messages( $uid, $type, $page, $size, $last_updated ){
        return self::with('comment')
            ->Own( $uid )
            ->typeOf( $type )
            ->valid()
            //->where('update_time','<', $last_updated)
            ->forPage( $page, $size )
            ->get();
    }

    public function get_follow_messages( $uid, $type, $page, $size, $last_updated ){
        return $this->Own( $uid )
            ->typeOf( $type )
            ->valid()
            ->forPage( $page, $size )
            ->get();
    }
}
