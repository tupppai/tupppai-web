<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends ModelBase
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'nickname', 'sex', 'avatar', 'phone'];
    protected $hidden = ['password'];
    protected $primaryKey = 'uid';

    public function setBalanceAttribute($value)
    {
        if(0 > $value){
            return error('AMOUNT_ERROR','金额不能为负数');
        }

        $this->attributes['balance'] = $value;
    }

    public function setFreezingAttribute($value)
    {
        if(0 > $value){
            return error('AMOUNT_ERROR','金额不能为负数');
        }
        $this->attributes['freezing'] = $value;
    }

    /**
     * 设置默认值
     */
    public function beforeCreate()
    {
        $this->sex = 0;

        $this->status = self::STATUS_NORMAL;
        $this->login_ip = get_client_ip();

        return $this;
    }

    public function afterFetch()
    {
        $location = explode('|', $this->location);
        if (sizeof($location) < 3) {
            $this->province = 0;
            $this->city = 0;
            $this->location = $this->location;
        } else {
            $this->province = intval($location[0]);
            $this->city = intval($location[1]);
            $this->location = $location[2];
        }
    }

    public function get_user_by_uids($uids, $page = 0, $limit = 0)
    {
        if (empty($uids)) {
            return array();
        }
        $query = self::query_builder();

        //默认根据uid获取数据
        $query->whereIn('uid', $uids);

        return self::query_page($query, $page, $limit);
    }

    public function get_user_by_uid($uid)
    {
        return self::whereUid($uid)->first();
    }

    public function get_user_by_phone($phone)
    {
        return self::wherePhone($phone)->first();
    }

    public function get_user_by_username($username)
    {
        return self::whereUsername($username)->first();
    }

    public function get_user_by_nickname($nickname)
    {
        return self::whereNickname($nickname)->first();
    }

    public function get_fuzzy_users_by_name($username)
    {
        return self::where('username', 'LIKE', "%$username%")->get();
    }

    public function search_users_by_name($name, $page, $size)
    {
        //return self::where( 'username', 'LIKE', "%$name%")
        return self::where('nickname', 'LIKE', "%$name%")
            ->where(function($query){
                $query = $query->where('users.status','>', self::STATUS_DELETED)
                        ->orWhere(function($q){
                            $q = $q->where('uid', _uid())
                                    ->where('users.status', '!=', self::STATUS_BLOCKED);
                        });
            })
            ->forPage($page, $size)
            ->get();
    }

    public function search_users_by_id_username_nickname($q)
    {
        return $this->where('uid', $q)
            ->orwhere('nickname', 'LIKE', '%' . $q . '%')
            ->orwhere('username', 'LIKE', '%' . $q . '%')
            ->select(['uid', 'nickname', 'username', 'status', 'sex', 'avatar'])
            ->get();
    }

    public function search_valid_users_by_id_username_nickname($q)
    {
        return $this->where('status', '>', self::STATUS_DELETED )
            ->where(function($query) use ($q) {
                $query->where('uid', $q)
                ->orwhere('nickname', 'LIKE', '%' . $q . '%')
                ->orwhere('username', 'LIKE', '%' . $q . '%');
            })
            ->select(['uid', 'nickname', 'username', 'status', 'sex', 'avatar'])
            ->forPage(1, 10)
            ->get();
    }

    public function get_users_by_downloads($ask_id)
    {
        $uids = $this->from('downloads')
            ->select('uid')
            ->where('target_id', $ask_id)
            ->where('type', self::TYPE_ASK)->get();
        return $this->whereIn('users.uid', $uids)->forPage(0, 10)->get();
    }
    public function count_users_by_downloads($ask_id)
    {
        return $this->from('downloads')
                ->where('target_id', $ask_id)
                ->where('type', self::TYPE_ASK)->count();
    }

    /**
     * 获取用户余额
     */
    public function get_user_balance($uid)
    {
        $user = $this->where('uid', $uid)->first();

        if ($user) {
            return $user->balance;
        }
        return 0;
    }

    /**
     * 获取用户冻结金额
     */
    public function get_user_freezing($uid)
    {
        $user = $this->where('uid', $uid)->first();

        if ($user) {
            return $user->freezing;
        }
        return 0;
    }

    public function check_uid_exist( $uid ){
        return $this->where('uid', $uid)->valid()->exists();
    }

}
