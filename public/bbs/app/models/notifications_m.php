<?php

class Notifications_m extends SB_Model
{

	function __construct ()
	{
		parent::__construct();

	}
	
	//@提醒someone
	public function notice_insert($topic_id,$suid,$nuid,$ntype)
	{
		$notics = array(
			'topic_id' => $topic_id,
			'suid' => $suid,
			'nuid' => $nuid,
			'ntype' => $ntype,
			'ntime' => time()
		);
		$this->db->insert('notifications',$notics);
	}

	public function get_notifications_list($nuid,$num)
	{
		$this->db->select("a.*,b.title,c.username, c.avatar");
		$this->db->from('notifications a');
		$this->db->where('a.nuid',$nuid);
		$this->db->join('topics b','b.topic_id = a.topic_id','LEFT');
		$this->db->join('users c','c.uid = a.suid','LEFT');
		$this->db->order_by('a.ntime','desc');
		$this->db->limit($num);
		$query = $this->db->get();
		if($query->num_rows() > 0){
		return $query->result_array();
		}
	}


}
