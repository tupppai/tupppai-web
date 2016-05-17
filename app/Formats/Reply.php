<?php namespace App\Formats;

class Reply extends FormatBase
{
	public static function index($array)
	{
		if(empty($array)){
			return [];
		}
		$data['reply'] = $array['reply_id'];
		$data['ask_id'] = $array['ask_id'];
		$data['type'] = $array['type'];
		$data['avatar'] = $array['avatar'];
		$data['uid'] = $array['uid'];
		$data['create_time'] = $array['create_time'];
		$data['love_count'] = $array['love_count'];
		$data['up_count'] = $array['up_count'];
		$data['comment_count'] = $array['comment_count'];
		$data['uped_count'] = $array['uped_count'];
		$data['like_count'] = $array['like_count'];
		$data['image_url'] = $array['image_url'];
		return $data;

	}
}




?>