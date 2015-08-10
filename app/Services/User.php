<?php
namespace App\Services;

use \App\Models\User as mUser,
    \App\Models\UserLanding as mUserLanding;

use \App\Services\ActionLog as sActionLog,
    \App\Services\Follow as sFollow,
    \App\Services\Ask as sAsk,
    \App\Services\UserRole as sUserRole,
    \App\Services\Download as sDownload,
    \App\Services\Reply as sReply,
    \App\Services\Collection as sCollection,
    \App\Services\UserLanding as sUserLanding;


class User extends ServiceBase
{
    /**
     * 新添加用户
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $nickname 昵称
     * @param integer$phone    手机号码
     * @param string $email    邮箱地址
     * @param array  $options  其它。暂不支持
     */
    public static function addNewUser($username, $password, $nickname, $phone, $location='', $email='', $avatar='', $sex = mUser::SEX_MAN, $options=array())
    {
        $user = new mUser;
        $user->assign(array(
            'phone'=>$phone,
            'username'=>$username,
            'password'=>self::hash($password),
            'nickname'=>$nickname,
            'email'=>$email,
            'avatar'=>$avatar,
            'sex'=>$sex,
            'location'=>$location
        ));
        $ret = $user->save();
        #todo: action log

        return $ret;
    }

    public static function loginUser($phone, $username, $password) {
        if ( $phone )
            $user = self::getUserByPhone($phone);
        else
            $user = self::getUserByUsername($username);
        if ( !password_verify($password, $user->password) )
            return error('PASSWORD_NOT_MATCH');

        sActionLog::log(sActionLog::TYPE_LOGIN, array(), $user);
        return self::detail($user);
    }

    public static function getUserInfoByUid($uid){
        $user       = self::getUserByUid($uid);
        $role_str   = sUserRole::getRoleStrByUid($uid);

        $data = self::brief($user);
        $data['role_id'] = $role_str;

        return $data;
    }

    /**
     * 根据条件查找用户
     */
    public static function getUserByUid ( $uid, $columns = '*' ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_uid($uid);
        if (!$user) {
            return error('USER_NOT_EXIST');
        }

        return $user;
    }
    public static function getUserByPhone( $phone ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_phone($phone);
        sActionLog::log(sActionLog::TYPE_LOGIN, array(), $user);

        if (!$user) {
            return error('USER_NOT_EXIST');
        }

        return $user;
    }
    public static function getUserByUsername( $username ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_username($username);
        if (!$user) {
            return error('USER_NOT_EXIST');
        }

        return $user;
    }
    public static function getUserByNickname( $username ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_nickname($username);

        return $user;
    }
    public static function getUserByUids ( $uid_arr ) {
        $user = new mUser;
        $users = $user->get_user_by_uids($uid_arr);

        $data = array();
        foreach ($users as $user) {
            $data[] = self::brief($user);
        }
        return $data;
    }
    /**
     * 根据uid获取手机号码
     */
    public static function getPhoneByUid( $uid ){
        return self::getUserByUid( $uid, 'phone')->phone;
    }

    public static function brief ( $user ) {
        $data = array(
            'uid'       => $user->uid,
            'username'  => $user->username,
            'phone'     => $user->phone,
            'nickname'  => $user->nickname,
            'email'     => $user->email,
            'avatar'    => $user->avatar,
            'is_god'    => $user->is_god,
            'ps_score'  => $user->ps_score,
            'sex'       => intval($user->sex),
            'login_ip'  => $user->login_ip,
            'last_login_time'=> $user->last_login_time,
            'location'  => $user->location,
            'province'  => $user->province,
            'city'      => $user->city,
            'bg_image'  => $user->bg_image
        );

        return $data;
    }

    /**
     * 格式化用户数据
     */
    public static function detail ( $user ) {
        if(!isset($user->current_score))
            $user->current_score = 0;
        if(!isset($user->paid_score))
            $user->paid_score = 0;
        if(!isset($user->total_praise))
            $user->total_praise = 0;

        $data = array(
            'uid'          => $user->uid,
            'nickname'     => $user->nickname,
            'sex'          => intval($user->sex),
            'avatar'       => $user->avatar,
            'uped_count'   => $user->uped_count,
            'current_score'=> $user->current_score,
            'paid_score'   => $user->paid_score,
            'total_praise' => $user->total_praise,
            'location'     => $user->location,
            'province'     => $user->province,
            'city'         => $user->city,
            'bg_image'     => $user->bg_image,
            'status'       => 1, //登陆成功
        );
        sUserLanding::getUserLandings($user->uid, $data);

        $data['fellow_count']     = sFollow::getUserFansCount($user->uid);
        $data['fans_count']       = sFollow::getUserFollowCount($user->uid);

        $data['ask_count']        = sAsk::getUserAskCount($user->uid);
        $data['reply_count']      = sReply::getUserReplyCount($user->uid);

        $data['inprogress_count'] = sDownload::getUserDownloadCount($user->uid);
        $data['collection_count'] = sCollection::getUserCollectionCount($user->uid);

        return $data;
    }

    /**
     * 静态获取被举报总数
     */
    public static function getAllInformCount($uid)
    {
        return self::getAskInformCount($uid) + self::getReplyInformCount($uid);
    }

    /**
     * 设置为大神
     */
    public static function setMaster($uid){
        $user = User::findFirst($uid);
        if( !$user ){
            return error('USER_NOT_EXIST');
        }
        $user->is_god = (int)!$user->is_god;

        return $user->save();
    }

    /**
     * 密码加密
     */
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 密码验证
     */
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
