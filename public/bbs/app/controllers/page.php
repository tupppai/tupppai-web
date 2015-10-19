<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends SB_Controller
{
	function __construct ()
	{
		parent::__construct();
		$this->load->model('page_m');
	}

	public function index($pid)
	{
		$data['page'] = $this->page_m->get_page_content($pid,0);
		$data['title'] = $data['page']['title'];
		$this->load->view('page',$data);
	}
}
