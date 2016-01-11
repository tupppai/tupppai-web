<?php
namespace App\Models;

class Feedback extends ModelBase{
    const STATUS_DELETED = 'DELETED';
    const STATUS_SUSPEND = 'SUSPEND';
    const STATUS_FOLLOWED = 'FOLLOWED';
    const STATUS_RESOLVED = 'RESOLVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_DONE = 'DONE';

    static $status_name = array(
        'DELETED'  => '已删除',
        'SUSPEND'  => '待处理',
        'FOLLOWED' => '已跟进',
        'RESOLVED' => '已解决',
        'REJECTED' => '不回应'
    );

    static $next_status = array(
        'DELETED'  => self::STATUS_SUSPEND,
        'SUSPEND'  => self::STATUS_FOLLOWED,
        'FOLLOWED' => self::STATUS_RESOLVED,
        'RESOLVED' => self::STATUS_DONE,
        'REJECTED' => self::STATUS_FOLLOWED
    );

    protected $table = 'feedbacks';

    public function beforeCreate(){
        $this->create_time = time();
        $this->update_time = time();
        $this->update_by   = 0;
        $this->opinion     = '{}';
        $this->status      = self::STATUS_SUSPEND;

        return $this;
    }

    public function get_feedback_by_fb_id( $fb_id ){
        return self::where( 'id', $fb_id )->first();
    }


    public function post_opinion( $content, $username ){
        $old_opinion = json_decode( $this->opinion, true );
        //prepend
        array_unshift($old_opinion, array(
            'username'=>$username,
            'comment_time'=> time(),
            'opinion'=>$content
        ));
        $this->opinion = json_encode( $old_opinion );
        $this->save();
        return $this;
    }

    //public static function get_status_name( $status_name ){
    //public static function change_status_to( $fb, $status, $uid ){
    //public function new_feedback( $content, $contact, $uid = 0){
    //public static function post_opinion( $fbid, $uid, $opinion ){
}
