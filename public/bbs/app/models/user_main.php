<?php

class User_main extends SB_Model
{
	function __construct ()
	{
        parent::__construct();
    }

    function fetch_main_user($uid) {
        $user = $this->db->query("select * from psgod.users where uid = $uid");
        return $user->row_array();
    }

    function add_main_user($uid, $username, $password, $email, $avatar){
        $salt =get_salt();
	    $password= password_dohash($password,$salt);
        $user = array(
            //默认到普通用户分组
            'uid'=>$uid,
            'group_type'=>2,
            'gid'=>3,
            'is_active'=>1,
            'username' => $username,
            'password' => $password,
            'avatar'=>$avatar,
            'salt' => $salt,
            'email' =>$email,
            'regtime' => time(),
            'ip' => get_onlineip()
        );
		return $this->db->insert('users',$user);
    }
}
