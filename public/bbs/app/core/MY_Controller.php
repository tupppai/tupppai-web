<?php
/**
 * The base controller which is used by the Front and the Admin controllers
 */
class Base_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();	
	}
}
class SB_Controller extends Base_Controller
{
	var $nodes	= '';
    var $pages = '';
    public $_user = null;
	function __construct(){
        parent::__construct();
        $this->load->model ('user_m');
		//判断关闭
		if($this->config->item('site_close')=='off'){
			show_error($this->config->item('site_close_msg'),500,'网站关闭');
		}		
		//载入前台模板
        $this->load->set_front_theme($this->config->item('themes'));
        /*
		//判断安装
		$file=FCPATH.'install.lock';
		if (!is_file($file)){
			redirect(site_url('install'));
        }
         */
		$this->load->database();
	 	//网站设定
		$data['items']=$this->db->get('settings')->result_array();
		$data['settings']=array(
			'site_name'=>$data['items'][0]['value'],
			'welcome_tip'=>$data['items'][1]['value'],
			'short_intro'=>$data['items'][2]['value'],
			'show_captcha'=>$data['items'][3]['value'],
			'site_run'=>$data['items'][4]['value'],
			'site_stats'=>$data['items'][5]['value'],
			'site_keywords'=>$data['items'][6]['value'],
			'site_description'=>$data['items'][7]['value'],
			'money_title'=>$data['items'][8]['value'],
			'per_page_num'=>$data['items'][9]['value'],
			'logo'=>$this->config->item('logo')
        );
        //用户相关信息
        if ($uid = $this->session->userdata('uid')) {
            
            $user = $this->db->where('uid',$uid)
                ->get('users')->row_array();
            //jq，如果当前论坛没有这个账号，就新建一个
            $this->load->model ('user_main', 'user');
            $main_user = $this->user->fetch_main_user($uid);
            if(!$user) {
                $this->user->add_main_user(
                    $main_user['uid'],
                    $main_user['nickname'],
                    $main_user['password'],
                    $main_user['email'],
                    $main_user['avatar']
                );
                $user = $this->db->where('uid',$uid)
                    ->get('users')->row_array();
            }
            $flag = false;

            if($main_user['nickname'] != $user['username']) {
                $flag = true;
                $user['username'] = $main_user['nickname'];
            }
            if($main_user['avatar'] != $user['avatar']) {
                $flag = true;
                $user['avatar'] = $main_user['avatar'];
            }
            /*
            if($main_user['username'] == '' && $main_user['nickname'] != $user['username']) {
                $flag = true;
                $user['username'] = $main_user['nickname'];
            }
             */
            if($flag) 
                $this->user_m->update_user($uid, $user);

            $user = $this->user_m->get_user_by_uid($uid);

            $notices= $this->db->select('notices')
                ->where('uid',$uid)
                ->get('users')->row_array();
	        $data['myinfo']=array(
				'uid'=>$user['uid'],
				'username'=>$user['username'],
				'avatar'=>$user['avatar'],
				'group_type'=>$user['group_type'],
				'gid'=>$user['gid'],
				'group_name'=>$user['group_name'],
				'is_active'=>$user['is_active'],
				'favorites'=>$user['favorites'],
				'follows'=>$user['follows'],
				'credit'=>$user['credit'],
				'notices'=>@$notices['notices'],
				'messages_unread'=>$user['messages_unread'],
				'lastpost'=>$user['lastpost']
            );

            $this->_user = $user;
        }

		//获取二级目录
		$data['base_folder'] = $this->config->item('base_folder');

		//底部菜单(单页面)
		$this->load->model('page_m');
		$data['page_links'] = $this->page_m->get_page_menu(10,0);
		//模板目录
		$data['themes']=base_url('static/'.$this->config->item('themes').'/');
		//全局输出
		$this->load->vars($data);
	}	
}
class Admin_Controller extends Base_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		//载入后台模板
		$this->load->set_admin_theme();
	 	//网站设定
		$data['items']=$this->db->get('settings')->result_array();
		$data['settings']=array(
			'site_name'=>$data['items'][0]['value'],
			'welcome_tip'=>$data['items'][1]['value'],
			'short_intro'=>$data['items'][2]['value'],
			'show_captcha'=>$data['items'][3]['value'],
			'site_run'=>$data['items'][4]['value'],
			'site_stats'=>$data['items'][5]['value'],
			'site_keywords'=>$data['items'][6]['value'],
			'site_description'=>$data['items'][7]['value'],
			'money_title'=>$data['items'][8]['value'],
			'per_page_num'=>$data['items'][9]['value']
		 );
		$this->load->vars($data);
	}
}
class Install_Controller extends Base_Controller 
{
	function __construct()
	{
		parent::__construct();
		//载入前台模板
		$this->load->set_front_theme('default');
	}
}
class Other_Controller extends Base_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		//载入前台模板
		$this->load->set_front_theme('default');
	}
}
