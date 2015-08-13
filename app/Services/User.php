<?php
namespace App\Services;

use \App\Models\User as mUser,
    \App\Models\UserLanding as mUserLanding,
    \App\Models\Follow as mFollow;

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


    public static function loginUser($phone, $username, $password) {
        sActionLog::init( 'LOGIN' );

        if ( $phone ){
            $user = self::getUserByPhone($phone);
        }
        else{
            $user = self::getUserByUsername($username);
        }

        if( !$user ){
            return error('USER_NOT_EXIST');
        }

        if ( !password_verify($password, $user->password) ){
            return error('PASSWORD_NOT_MATCH');
        }

        sActionLog::save( $user );
        return self::detail($user);
    }

    public static function checkHasRegistered( $type, $value ){
        //Check registered account.
        if( $type == 'mobile' ){
            $mUser = new mUser();
            $user = $mUser->get_user_by_phone($value);
            if( $user ){
                return true;
                //turn to login
                return error( 'WRONG_ARGUMENTS', '手机已注册' );
            }
        }
        else{
            if(sUserLanding::getUserByOpenid($value, sUserLanding::getLandingType($type))){
                return true;
                //turn to login
                return $this->output( '注册失败！该账号已授权，用户已存在。' );
            }
        }
        return false;
    }

    public static function addUser( $type, $username, $password, $nickname, $mobile, $location, $avatar, $sex, $openid=''){
        $user = new mUser();
        sActionLog::init( 'REGISTER' );

        $user =self::addNewUser($username, $password, $nickname, $mobile, $location, $avatar, $sex );
        if( $type != 'mobile' ){
            self::addNewUserLanding($user->uid, $openid, $type);
        }
        sActionLog::save( $user );
        return $user;
    }

    /**
     * 新添加用户
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $nickname 昵称
     * @param integer$phone    手机号码
     * @param string $email    邮箱地址
     */
    public static function addNewUser($username, $password, $nickname, $phone, $location='', $avatar='', $sex = mUser::SEX_MAN){
        $user = new mUser;
        $user->assign(array(
            'username'=>$username,
            'password'=>self::hash($password),
            'nickname'=>$nickname,
            'phone'=>$phone,
            'location'=>$location,
            'avatar'=>$avatar,
            'sex'=>$sex,
            'email'=>'',
        ));
        $ret = $user->save();
        #todo: action log

        return $ret;
    }

    public static function resetPassword( $phone, $password ){
        // find user
        $user = self::getUserByPhone($phone);
        if( !$user ){
            return error('USER_NOT_EXIST');
        }

        sActionLog::init( 'RESET_PASSWORD', $user );
        // set password
        $user->password = self::hash( $password );
        $user->save();
        sActionLog::save( $user );

        return true;
    }

    /**
     * 增加用户的求助数量
     */
    public static function addUserAskCount( $uid ) {
        return (new mUser)->increase_asks_count($uid);
    }

    public static function getFans( $uid ){
        $mFollow = new mFollow();
        $fans = $mFollow->get_user_fans( $uid );
        $mUser = new mUser();

        $fansList = array();
        foreach( $fans as $key => $value ){
            $fansList[] = self::detail( $mUser->get_user_by_uid( $value->uid ) );
        }

        return $fansList;
    }

     public static function getFriends( $uid ){
        $mFollow = new mFollow();
        $friends = $mFollow->get_user_friends( $uid );
        $mUser = new mUser();

        $friendsList = array();
        foreach( $friends as $key => $value ){
            $fansList[] = self::detail( $mUser->get_user_by_uid( $value->follow_who ) );
        }

        return $fansList;
    }

    public static function updatePassword( $uid, $oldPassword, $newPassword ){
        $mUser = new mUser();
        $user = $mUser->get_user_by_uid( $uid );
        if( !$user ){
            return false;
        }

        if( !User::verify( $oldPassword, $user->password ) ){
            return error( 'WRONG_ARGUMENTS', '原密码错误');
        }

        $user->password = self::hash( $newPassword );
        $user->save();

        return true;
    }

    public static function updateProfile( $uid, $nickname, $avatar, $sex, $location, $city, $province ){
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_uid($uid);
        if (!$user) {
            return error('USER_NOT_EXIST');
        }
        sActionLog::init( 'MODIFY_USER_INFO', $user );

        if( $nickname ){
            $user->nickname = $nickname;
        }

        if( $avatar ){
            $user->avatar = $avatar;
        }

        if( $sex === '0' || $sex === '1' ){
            $user->sex = $sex;
        }

        if($location || $city || $province) {
            $location = $this->encode_location($province, $city, $location);
            $user->location = $location;
        }

        $user->update_time = time();
        $user->save();
        sActionLog::save( $user );

        return true;

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
     * 密码加密
     */
    public static function hash($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 密码验证
     */
    public static function verify($password, $hash){
        return password_verify($password, $hash);
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

        return self::detail( $user );
    }
    public static function getUserByPhone( $phone ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_phone($phone);

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

}
