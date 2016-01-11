<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Group_m extends SB_Model
{

	function __construct ()
	{
		parent::__construct();
	}

	public function group_list()
	{
		$query = $this->db->order_by('gid')->get('user_groups');
		if($query->num_rows>0){
			return $query->result_array();
		} else
		{
			return false;
		}
	}
	public function get_group_info($gid)
	{
		$query = $this->db->get_where('user_groups',array('gid'=>$gid));
		if($query->num_rows>0){
			return $query->row_array();
		} else
		{
			return false;
		}
	}

	public function check_group($group_name)
	{
		$query = $this->db->get_where('user_groups',array('group_name'=>$group_name));
		if($query->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}

}
