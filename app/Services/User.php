<?php
namespace App\Services;
use DB;
use App\Models\User as mUser,
    App\Models\UserLanding as mUserLanding,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Collection as mCollection,
    App\Models\Focus as mFocus,
    App\Models\Count as mCount,
    App\Models\Role as mRole,
    App\Models\ThreadCategory as mThreadCategory,
    App\Models\Comment as mComment,
    App\Models\Message as mMessage,
    App\Models\Follow as mFollow;

use App\Services\ActionLog as sActionLog,
    App\Services\Follow as sFollow,
    App\Services\Ask as sAsk,
    App\Services\UserRole as sUserRole,
    App\Services\Download as sDownload,
    App\Services\Invitation as sInvitation,
    App\Services\Master as sMaster,
    App\Services\Reply as sReply,
    App\Services\SysMsg as sSysMsg,
    App\Services\Comment as sComment,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Count as sCount,
    App\Services\Usermeta as sUsermeta,
    App\Services\Sms as sSms,
    App\Services\Collection as sCollection,
    App\Services\UserLanding as sUserLanding;

use App\Counters\UserCounts as cUserCounts;

use App\Counters\UserFollows as cUserFollows;

use App\Facades\CloudCDN;

class User extends ServiceBase
{

    public static function loginUser($phone, $username, $password, $type = 'mobile') {
        sActionLog::init( 'LOGIN' );

        if ( $phone ){
            $user = self::getUserByPhone($phone);
        }
        else{
            $user = self::getUserByUsername($username);
        }

        if( !$user ){
            return array(
                'status'=>3
            );
            //return error('USER_NOT_EXIST', '用户不存在');
        }

        if ( $type == 'mobile' && !password_verify($password, $user->password) ){
            return array(
                'status'=>2
            );
            //return error('PASSWORD_NOT_MATCH', '账号或者密码不对');
        }
        $user->last_login_time = time();
        $user->save();
        sActionLog::save( $user );
        return self::detail($user);
    }

    public static function checkHasRegistered( $type, $value ){
        //Check registered account.
        if ( $type == 'mobile' ){
            if ( (new mUser)->get_user_by_phone($value) ) {
                return error( 'USER_EXISTS', '手机已注册' );
            }
        }
        else {
            if (sUserLanding::getUserByOpenid($value, sUserLanding::getLandingType($type))){
                return error( 'WRONG_AUTHORIZATION_EXIST','注册失败！该账号已授权，用户已存在' );
            }
        }

        return false;
    }

    public static function checkRegistered( $type, $value ){
        //Check registered account.
        if ( $type == 'mobile' ){
            if ( (new mUser)->get_user_by_phone($value) ) {
                return true;
            }
        }
        else {
            if (sUserLanding::getUserByOpenid($value, sUserLanding::getLandingType($type))){
                return true;
            }
        }
        return false;
    }

    public static function addUser( $type, $username, $password, $nickname, $mobile, $location, $avatar, $sex, $openid=''){
        $user = new mUser();

        $user =self::addNewUser($username, $password, $nickname, $mobile, $location, $avatar, $sex );
        if( $type != 'mobile' ){
            sUserLanding::addNewUserLanding($user->uid, $openid, $nickname, $type);
        }
        $role = sUserRole::assignRole($user->uid, mUser::ROLE_NEWBIE );
        return $user;
    }

    public static function addWaistcoatUser( $username, $password, $nickname, $sex, $phone, $avatar, $role_id ){
        $mUser = new mUser();
        if( $mUser->where( 'username', $username )->exists() ){
            return error( 'USER_EXISTS', '用户已存在');
        }
        if( $mUser->where('nickname', $nickname )->exists() ){
            return error( 'NICKNAME_EXISTS', '该昵称已被注册' );
        }

        $phone += $mUser->count();
        $user = self::addNewUser($username,$password,$nickname, $phone, 0, "", $avatar, $sex);
        if( !$user ){
            return error('ADD_USER_FAILD', '保存失败'.$user->getMessages());
        }
        $role = sUserRole::assignRole($user->uid, $role_id);

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

        sActionLog::init( 'REGISTER' );
        $user->assign(array(
            'username'=>emoji_to_shortname($username),
            'password'=>self::hash($password),
            'nickname'=>emoji_to_shortname($nickname),
            'phone'=>$phone,
            'location'=>$location,
            'avatar'=>$avatar,
            'sex'=>$sex,
            'email'=>'',
        ));
        $ret = $user->save();

        //更新短信发送的记录
        sSms::updateSms($phone);

        sActionLog::save( $ret );

        // 自己关注自己
        sFollow::follow( $ret->uid, $ret->uid, mUser::STATUS_NORMAL);
        return $ret;
    }

    public static function resetPassword( $phone, $password ){
        // find user
        $user = self::getUserByPhone($phone);
        if( !$user ){
            return error('USER_NOT_EXIST', '用户不存在');
        }

        sActionLog::init( 'RESET_PASSWORD', $user );
        // set password
        $user->password = self::hash( $password );
        $user->save();
        sActionLog::save( $user );

        return true;
    }

    public static function getFans( $myUid, $uid, $page, $size ){
        $mFollow = new mFollow();
        $fans = $mFollow->get_user_fans( $uid, $page, $size );
        $mUser = new mUser();

        $fansList = array();
        foreach( $fans as $key => $value ){
            $fan = self::detail( $mUser->get_user_by_uid( $value->uid ) );
            //我与他的关系
            $fansList[] = self::addRelation( $myUid, $fan );
        }

        return $fansList;
    }

    public static function getFriends( $myUid, $uid, $page, $size, $ask_id = 0 ){
        $mFollow = new mFollow();
        $friends = $mFollow->get_user_friends( $uid, $page, $size );
        $mUser = new mUser();

        $friendsList = array();
        foreach( $friends as $key => $friendUId ){
            $fan = self::detail( $mUser->get_user_by_uid( $friendUId ) );
            $friendsList[] = self::addRelation( $myUid, $fan, $ask_id );
        }

        return $friendsList;
    }

    public static function updatePassword( $uid, $oldPassword, $newPassword ){
        $mUser = new mUser();
        $user = $mUser->get_user_by_uid( $uid );
        if( !$user ){
            return error('USER_NOT_EXIST', '用户不存在');
        }
        sActionLog::init('CHANGE_PASSWORD', $user );

        if( !User::verify( $oldPassword, $user->password ) ){
            //return error( 'WRONG_ARGUMENTS', '原密码错误');
            return 2;
        }

        $user->password = self::hash( $newPassword );
        $user->save();

        return 1;
    }

    public static function updateProfile( $uid, $nickname, $avatar, $sex, $location, $city, $province ){
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_uid($uid);
        if (!$user) {
            return error('USER_NOT_EXIST', '用户不存在');
        }
        sActionLog::init( 'MODIFY_USER_INFO', $user );

        $nickname = emoji_to_shortname($nickname);

        if( $nickname && $nickname != $user->nickname && self::getUserByNickname($nickname) ){
            return error('NICKNAME_EXISTS');
        }

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
            $location = encode_location($province, $city, $location);
            $user->location = $location;
        }

        $user->update_time = time();
        $user->save();
        sActionLog::save( $user );

        return true;
    }

    /**
     * 模糊查询用户名
     */
    public static function getFuzzyUserIdsByName($name){
        $user_ids = array();
        $users = (new mUser)->get_fuzzy_users_by_name($name);
        foreach($users as $user) {
            $user_ids [] = $user->uid;
        }

        return $user_ids;
    }

    public static function getFuzzyUsersByIdAndName( $q ){
        return (new mUser)->search_users_by_id_username_nickname( $q );
    }

    public static function searchUserByName($name, $page, $size) {
        $data  = array();
        $users = (new mUser)->search_users_by_name($name, $page, $size);
        foreach($users as $user) {
            $user = self::detail($user);
            $user = self::addRelation( _uid(), $user );
            $user['replies'] = sReply::getBriefReplies(array(
                'uid'=>$user['uid']
            ), 0, 4);

            $data[] = $user;
        }
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

    public static function addRelation( $uid, $userArray, $askId = 0 ){
        //dd($userArray);
        $userArray['is_follow']    = (int)sFollow::checkRelationshipBetween( $uid, $userArray['uid'] );
        $userArray['is_fan']       = (int)sFollow::checkRelationshipBetween( $userArray['uid'], $uid );
        $userArray['is_block']     = (int)sFollow::checkIsBlocked($uid, $userArray['uid']);
        $userArray['is_star']      = (bool)sUserRole::checkUserIsStar( $userArray['uid'] );
        $userArray['has_invited']  = sInvitation::checkInvitationOf( $askId, $userArray['uid'] );

        return $userArray;
    }

    /**
     * 获取管理后台用户信息
     */
    public static function getUserInfoByUid($uid){
        $user       = self::getUserByUid($uid);
        $role_str   = sUserRole::getRoleStrByUid($uid);

        $data       = self::detail($user);
        $data['role_id'] = $role_str;

        return $data;
    }

    public static function getValidUsers(){
        return mUser::valid()->get();
    }
    /**
     * 根据条件查找用户
     */
    public static function getUserByUid ( $uid, $columns = '' ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_uid($uid);
        /*
        if (!$user) {
            return error('USER_NOT_EXIST');
        }
         */

        return $user;
    }
    public static function getUserByPhone( $phone ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_phone($phone);

        return $user;
    }
    public static function getUserByUsername( $username ) {
        $mUser = new mUser();
        //$mUser->set_columns($columns);
        $user = $mUser->get_user_by_username($username);
        if (!$user) {
            return error('USER_NOT_EXIST', '用户不存在');
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
     * 设置为大神
     */
    public static function setMaster( $uid, $status ){
        $mUser = new mUser;
        // $user = $mUser->get_user_by_uid($uid);
        // if( !$user ){
        //     return error('USER_NOT_EXIST');
        // }
        sActionLog::init( 'SET_MASTER' );
        $user = $mUser->where( 'uid', $uid )->update( ['is_god'=>$status] );
        sActionLog::save( $user );
        return $user;
    }

    public static function getSubscribed( $uid, $page, $size, $last_updated ){
        $mCollection = new mCollection();
        $mFocus = new mFocus();

        $collections = DB::table('collections')
            ->selectRaw('reply_id as target_id, '. mCollection::TYPE_REPLY.' as target_type, collections.update_time')
            ->where('collections.uid', $uid)
            ->where('collections.status',mCollection::STATUS_NORMAL)
            ->where('collections.update_time','<', $last_updated)
            ->join('replies', 'replies.id','=','reply_id')
            ->where(function( $query) use ($uid){
                $query->where('replies.status','>', mReply::STATUS_DELETED)
                      ->orWhere(['replies.uid'=>$uid, 'replies.status'=>mReply::STATUS_BLOCKED]);
            });
        $focuses = DB::table('focuses')
            ->selectRaw('ask_id as target_id, '. mFocus::TYPE_ASK.' as target_type, focuses.update_time')
            ->where('focuses.uid', $uid)
            ->where('focuses.status', mFocus::STATUS_NORMAL)
            ->where('focuses.update_time','<', $last_updated)
            ->join('asks','asks.id','=','ask_id' )
            ->where(function($query) use ($uid){
                $query->where('asks.status','>', mAsk::STATUS_DELETED )
                      ->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> mAsk::STATUS_BLOCKED ]); //加上自己的广告贴
            });

        $colFocus = $focuses->union($collections)
            ->orderBy('update_time','DESC')
            ->forPage( $page, $size )
            ->get();

        $subscribed = self::parseAskAndReply( $colFocus );

        return $subscribed;
    }

    private static function parseAskAndReply( $threads ){
        $subscribed = array();
        foreach( $threads as $key=>$value ){
            switch( $value->target_type ){
                case mCollection::TYPE_REPLY:
                    $reply = sReply::detail( sReply::getReplyById($value->target_id) );
                    array_push( $subscribed, $reply );
                    break;
                case mFocus::TYPE_ASK:
                    $is_tutorial = sThreadCategory::checkedThreadAsCategoryType( mAsk::TYPE_ASK, $value->target_id, mThreadCategory::CATEGORY_TYPE_TUTORIAL );
                    if( $is_tutorial ){
                        $ask = sAsk::tutorialDetail( sAsk::getAskById( $value->target_id, false) );
                    }
                    else{
                        $ask = sAsk::detail( sAsk::getAskById( $value->target_id, false) );
                    }
                    array_push( $subscribed, $ask );
                    break;
            }
        }

        return $subscribed;
    }

    public static function getTimelineThread($uid,  $page, $size ,$last_updated ){
        $friends = sFollow::getUserFollowByUid( $uid );

        $asks = DB::table('asks')
            ->whereIn( 'uid', $friends )
            ->where('update_time','<', $last_updated )
            ->selectRaw('id as target_id, '. mAsk::TYPE_ASK.' as target_type, update_time, create_time')
            ->where(function($query) use ($uid){
                $query->where('asks.status','>', mAsk::STATUS_DELETED )
                      ->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> mAsk::STATUS_BLOCKED ]); //加上自己的广告贴
            });
        $replys = DB::table('replies')
            ->whereIn( 'uid', $friends )
            ->where('update_time','<', $last_updated )
            ->selectRaw('id as target_id, '. mAsk::TYPE_REPLY.' as target_type, update_time, create_time')
            ->where(function($query) use ($uid){
                $query->where('replies.status','>', mReply::STATUS_DELETED )
                    ->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> mAsk::STATUS_BLOCKED ]); //加上自己的广告贴
            });

        $askAndReply = $replys->union($asks)
            ->orderBy('create_time', 'DESC')
            ->orderBy('target_id','DESC')
            ->forPage( $page, $size )
            ->get();

        $timelines = self::parseAskAndReply( $askAndReply );

        return $timelines;
    }

    public static function blockUser( $uid, $status ){
        $mUser = new mUser();
        if( $status == -1 ){
            $user = mUser::where('uid', $uid )->update(['status' => mUser::STATUS_BANNED]);
            sUserRole::assignRoleToUser( $uid, mUser::ROLE_BLOCKED );
            sAsk::blockUserAsks( $uid );
            sReply::blockUserReplies( $uid );
            sComment::blockUserComments( $uid );
        }
        else if( $status == 1 ){
            $user = mUser::where('uid', $uid )->update(['status' => mUser::STATUS_NORMAL]);
            sUserRole::revokeRoleFromUser( $uid, mUser::ROLE_BLOCKED );
            sAsk::recoverBlockedAsks( $uid );
            sReply::recoverBlockedReplies( $uid );
            sComment::recoverBlockedComments( $uid );
        }
        return $user;
    }

    public static function setRole( $uid, $role_id ){
        $mUser = new mUser();
        $user = mUser::where('uid', $uid )->update(['role' => $role_id]);
        return $user;
    }


    public static function getThreadsByUid( $uid, $page, $size, $last_updated ){
        $asks = DB::table('asks')
            ->where( 'uid', $uid )
            ->where('update_time','<', $last_updated )
            ->selectRaw('id as target_id, '. mAsk::TYPE_ASK.' as target_type, update_time')
            ->where('status','>', 0 );
        $replys = DB::table('replies')
            ->where( 'uid', $uid )
            ->where('update_time','<', $last_updated )
            ->selectRaw('id as target_id, '. mAsk::TYPE_REPLY.' as target_type, update_time')
            ->where('status','>', 0 );

        $askAndReply = $replys->union($asks)
            ->orderBy('update_time','DESC')
            ->orderBy('target_type', 'ASC')
            ->orderBy('target_id','DESC')
            ->forPage( $page, $size )
            ->get();

        $timelines = self::parseAskAndReply( $askAndReply );

        return $timelines;
    }

    public static function setRemarkForUser( $uid, $nickname, $password, $is_reset, $remark ){
        $mUser = new mUser();
        $u = $mUser->where( 'uid', $uid )->first( );
        if( !$u ){
            return error('USER_NOT_EXIST', '用户不存在');
        }

        ActionLog::init( 'MODIFY_REMARK', $u );

        $nickname = emoji_to_shortname($nickname);
        if($nickname){
            $u->nickname = $nickname;
        }

        if($remark){
            $oldRemark = sUsermeta::read_user_remark( $uid );
            sUsermeta::write_user_remark( $uid, $remark );
            sActionLog::save( ['remark'=>$oldRemark], ['remark'=>$remark] );
        }

        if( isset($is_reset) && $is_reset ){
            $u->password = sUser::hash($password);
        }
        $u->save();
        sActionLog::init( 'MODIFY_USER_INFO' );
        sActionLog::save( $u );
        return true;
    }


    public static function banUser( $uid, $value ){
        $mUser = new mUser();
        $user = $mUser->get_user_by_uid($uid);
        if(!$user) {
            return error('USER_NOT_EXIST', '用户不存在');
        }

        $old = sUsermeta::read_user_forbid($uid);

        sActionLog::init('FORBID_USER');
        $res = sUsermeta::write_user_forbid($uid, $value);
        sActionLog::save( array('fobid'=>$old), array('fobid'=>$res) );
    }

    public static function isBlocked( $uid ){
        $mUser = new mUser();
        $user = $mUser->get_user_by_uid($uid);
        if(!$user) {
            return error('USER_NOT_EXIST', '用户不存在');
        }
        $roles = sUserRole::getRoleStrByUid( $uid );

        return in_array(mRole::ROLE_BLOCKED, $roles);
    }

    /**
     * 获取账户余额
     */
    public static function getUserBalance($uid) {
        return (new mUser)->get_user_balance($uid);
    }
    /**
     * 获取账户冻结金额
     */
    public static function getUserFreezing($uid) {
        return (new mUser)->get_user_freezing($uid);
    }


    /**
     * 精简输出
     */
    public static function brief ( $user ) {
        $counts = cUserCounts::get( $user->uid );
        $data = array(
            'uid'       => $user->uid,
            'username'  => $user->username,
            'phone'     => $user->phone,
            'nickname'  => shortname_to_unicode($user->nickname),
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
            'bg_image'  => $user->bg_image,
            'badges_count'   => $counts['badges_count']
        );
        if( $user->uid == _uid() ){
            $data['balance'] = $user->balance;
        }
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

        $location = decode_location( $user->location );

        $data = array(
            'uid'          => $user->uid,
            'username'     => $user->username,
            'nickname'     => shortname_to_unicode($user->nickname),
            'phone'        => $user->phone,
            'sex'          => $user->sex?1:0,
            'avatar'       => CloudCDN::file_url($user->avatar),
            'current_score'=> $user->current_score,
            'paid_score'   => $user->paid_score,
            'total_praise' => $user->total_praise,
            'location'     => $location['location'],
            'province'     => $location['province'],
            'city'         => $location['city'],
            'bg_image'     => $user->bg_image,
            'status'       => 1, //登陆成功
        );
        $data = self::addRelation( _uid(), $data );
        sUserLanding::getUserLandings($user->uid, $data);

        $data['uped_num']       = 0;

        $counts = cUserCounts::get( $user->uid );
        $data = array_merge( $data, $counts );
        return $data;
    }

    public static function countUserAmount(){
        return (new mUser)->where('status', '>', mUser::STATUS_DELETED)->count();
    }

    public static function getUserAvatarByUid( $uid ){
        return (new mUser)->where('uid', $uid)->pluck('avatar');
    }

}
