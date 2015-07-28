<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends ModelBase 
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password'];
    protected $hidden = ['password'];
    protected $primaryKey = 'uid';

    const SEX_MAN   = 1;
    const SEX_FEMALE= 0;

    /**
     * 更新时间
     */
    public function beforeSave() {
        $this->update_time  = time();

        return $this;
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status        = mUser::STATUS_NORMAL;
        $this->update_time   = time();
        $this->create_time   = time();
        $this->login_ip      = get_client_ip();

        return $this;
    }


    public function afterFetch() {
        $location = explode('|', $this->location);
        if(sizeof($location) < 3){
            $this->province = 0;
            $this->city     = 0;
            $this->location = $this->location;
        }
        else {
            $this->province = intval($location[0]);
            $this->city     = intval($location[1]);
            $this->location = $location[2];
        }   
    }

    public function get_user_by_uids($uids, $page, $limit){
        $builder = self::query_builder();

        if( empty($uids) ){
            return array();
        }
        $builder->inWhere('uid', $uids);
        //默认根据uid获取数据
        //$builder->andWhere('status = :status:', array('status' => self::STATUS_NORMAL))
        return self::query_page($builder, $page, $limit);
    }

    public function all_inform_count()
    {
        return $this->ask_inform_count() + $this->reply_inform_count();
    }
    /**
     * 收藏总数
     */
    public function collection_count()
    {
        return Collection::count(array("uid = {$this->uid} AND status = ".Collection::STATUS_NORMAL));
    }

    /**
     * 关注求助总数
     */
    public function focus_count()
    {
        return Focus::count(array("uid = {$this->uid} AND status = ".Focus::STATUS_NORMAL));
    }

    /**
     * 进行中总数
     */
    public function inprogress_count()
    {
        return Download::count(array("uid = {$this->uid}")) - Reply::count(array("uid = {$this->uid}"));
    }

}
