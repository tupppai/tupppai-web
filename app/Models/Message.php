<?php namespace App\Models;

class Message extends ModelBase
{
    protected $table = 'messages';
    protected $fillable = ['status','update_time'];

    const MSG_SYSTEM  = 0; // 系统
    const MSG_COMMENT = 1; // 评论
    const MSG_REPLY   = 2; // 作品
    const MSG_ASK     = 2; // 求助
    const MSG_FOLLOW  = 3; // 关注
    const MSG_INVITE  = 4; // 邀请
    const MSG_LIKE    = 5; // 点赞

    /**  belongsTo  **/
    public function comment(){
        return $this->belongsTo('\App\Models\Comment','target_id');
    }

    public function reply(){
        return $this->belongsTo('\App\Models\Reply', 'target_id');
    }

    public function invite(){
        return $this->belongsTo('\App\Models\Ask', 'target_id');
    }

    /**  scopes  **/
    public function scopeOwn( $query, $uid ){
        $query->where('receiver', $uid);
    }

    public function scopeTypeOf( $query, $type ){
        $query->where('msg_type', $type);
    }

    public function scopeNormalMessage( $query ){
        $query->whereIn('msg_type', array(
            self::MSG_COMMENT,
            self::MSG_REPLY,
            self::MSG_FOLLOW,
            self::MSG_INVITE
        ));
    }

    public function scopeFoldMessage( $query ){
        $query->whereIn('msg_type', array(
            self::MSG_SYSTEM,
            self::MSG_LIKE
        ));
    }

    /** send messages **/
    public function send_new_message( $sender, $receiver, $msg_type, $content, $target_type, $target_id ){
		$msg = new mMessage();
		$msg->sender    = $sender;
		$msg->receiver  = $receiver;
		$msg->content   = $content;
		$msg->msg_type  = $msg_type;
		$msg->status    = mMessage::STATUS_NORMAL;
		$msg->target_id = $target_id;
		$msg->target_type = $target_type;
		$msg->create_time = time();
		$msg->update_time = time();
		return $msg->save();
    }

    public function get_messages( $uid, $type=null, $page = 1, $size = 15, $last_updated = NULL) {

        $builder = $this->Own( $uid )
            ->valid()
            ->forPage( $page, $size )
            ->orderBy('create_time', 'DESC');

        if($type == 'fold'){
            $builder = $builder->foldMessage();
        }
        else if($type = 'normal'){
            $builder = $builder->normalMessage();
        }

        if($last_updated) {
            $builder = $builder->where('create_time', '<', $last_updated);
        }
        return $builder->get();
    }

    /** get messages **/
    public function get_comment_messages( $uid, $page=1, $size=15, $last_updated = NULL ){
        return self::with('comment')
            ->Own( $uid )
            ->typeOf( self::MSG_COMMENT )
            ->valid()
            //->where('update_time','<', $last_updated)
            ->forPage( $page, $size )
            ->get();
    }

    public function get_follow_messages( $uid, $page=1, $size=15, $last_updated = NULL ){
        return $this->Own( $uid )
            ->typeOf( self::MSG_FOLLOW  )
            ->valid()
            ->forPage( $page, $size )
            ->get();
    }


    public function get_reply_message( $uid, $page=1, $size=15, $last_updated = NULL ){
        return self::with('reply')
            ->Own( $uid )
            ->typeOf( self::MSG_REPLY )
            ->valid()
            ->forPage( $page, $size )
            ->get();
    }

    public function get_invite_message( $uid, $page=1, $size=15, $last_updated = NULL ){
        return self::with('invite')
            ->Own( $uid )
            ->typeOf( self::MSG_INVITE )
            ->valid()
            ->forPage( $page, $size )
            ->get();
    }

    public function get_system_message( $uid, $page=1, $size=15, $last_updated = NULL ){
        return $this->Own( $uid )
            ->typeOf( self::MSG_SYSTEM )
            ->valid()
            ->forPage( $page, $size )
            ->get();
    }

    public function delete_messages_by_type( $uid, $type ){
        $msgs = $this->typeOf( $type )
        ->valid()
        ->Own( $uid )
        ->get();

        $this->batch_delete( $msgs );

        return true;
    }

    protected function batch_delete( $msgs ){
        foreach( $msgs as $msg ){
            $msg->assign(['status'=>self::STATUS_DELETED,'update_time'=>time()])->save();
        }
    }

    public function delete_messages_by_mids( $uid, $mids ){
        $msgs = $this->Own( $uid )
            ->valid()
            ->whereIn('id', explode(',', $mids))
            ->get();


        $this->batch_delete( $msgs );

        return true;
    }
}
