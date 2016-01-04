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

    public function scopeSend( $query, $uid) {
        $query->where('sender', $uid);
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

    public function get_messages( $uid, $type=null, $page = 1, $size = 15) {

        $builder = self::query_builder();
        if($type == 'fold'){
            $builder = $builder->foldMessage();
        }
        else {
            switch($type) {
            //send comment
            case 'send_comment':
                $builder = $builder->typeOf( self::MSG_COMMENT );
                $builder = $builder->Send($uid);
                break;
            //receive comment
            case 'comment':
                $builder = $builder->typeOf( self::MSG_COMMENT );
                $builder = $builder->Own($uid);
                break;
            case 'follow':
                $builder = $builder->typeOf( self::MSG_FOLLOW );
                $builder = $builder->Own($uid);
                break;
            case 'reply':
                $builder = $builder->typeOf( self::MSG_REPLY );
                $builder = $builder->Own($uid);
                break;
            case 'invite':
                $builder = $builder->typeOf( self::MSG_INVITE );
                $builder = $builder->Own($uid);
                break;
            case 'like':
                $builder = $builder->typeOf( self::MSG_LIKE );
                $builder = $builder->Own($uid);
                break;
            case 'system':
                $builder = $builder->typeOf( self::MSG_SYSTEM );
                $builder = $builder->Own($uid);
                break;
            default:
                //normal
                $builder = $builder->normalMessage();
                $builder = $builder->Own($uid);
                break;
            }
        }

        return self::query_page($builder, $page, $size);
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
