<?php

namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Follow extends ModelBase
{
    protected $table = 'follows';
    protected $fillable = ['uid','follow_who','status','create_time','update_time'];

    public function get_friend_relation_of( $uid, $friend_uid ){
        return $this->where( [
            'uid' => $uid,
            'follow_who' => $friend_uid
        ] )
        ->first();
    }

    public function update_friendship( $uid, $friend_uid, $status ){
        $cond = [
            'uid'=>$uid,
            'follow_who' => $friend_uid
        ];
        $friendship = $this->firstOrNew( $cond );
        $data = $cond;

        $data['update_time']    = time();
        $data['status']         = $status;

        //New
        if( !$friendship->id ){
            if( $status == self::STATUS_DELETED ){
                return true;
            }
            $data['create_time'] = time();
        }

        return (bool)$friendship->assign( $data )->save();
    }



    public function get_follower_users ( $uid, $page, $limit ) {
        $builder = self::query_builder();
        $builder->where('uid', $uid);
        $builder->orderBy('update_time DESC');
        return self::query_page($builder, $page, $limit);
    }

    public function get_fans_users ( $uid, $page, $limit ) {
        $builder = self::query_builder();
        $builder->where('follow_who', $uid);
        $builder->orderBy('update_time DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 获取粉丝数量
     */
    public function count_user_fans($uid) {
        $count = self::where('follow_who', '=', $uid)
            ->where('uid', '!=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 获取关注的人数量
     */
    public function count_user_followers($uid) {
        $count = self::where('uid', '=', $uid)
            ->where('follow_who','!=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 获取粉丝
     */
    public function get_user_fans( $uid, $page = null, $size = null) {

        $builder = self::where('follow_who', '=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->where('uid','!=', $uid)
            ->orderBy('id', 'desc')
            ->select('uid');

        if( $size ) {
            $builder = $builder->forPage( $page, $size );
        }
        $users = $builder->get();
        return $users;
    }

    public function get_user_followers( $uid ){
        $followsUids =  $this->where([
            'uid' => $uid,
            'status' => self::STATUS_NORMAL
            ])
            ->whereAnd('follow_who','!=', $uid)
            ->lists('follow_who');
        return $followsUids;
    }

    /**
     * 获取关注的人
     */
    public function get_user_friends( $uid, $page, $size ) {
        $users = self::where([
                'uid'=> $uid,
                'status'=> self::STATUS_NORMAL
            ])
            ->where('follow_who','!=', $uid)
            ->orderBy('id', 'desc')
            ->forPage( $page, $size )
            ->lists('follow_who');
        return $users;
    }

}
