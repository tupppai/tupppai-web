<?php

	namespace App\Formats;


	class Inprogresses
	{
		public static function index($array)
		{
			if(empty($array)){
				return [];
			}

			$data['ask_id'] = $array['ask_id'];
			$data['type'] = $array['type'];
			$data['avatar'] = $array['avatar'];
			$data['uid'] = $array['uid'];
			$data['nickname'] = $array['nickname'];
			$data['upload_id'] = $array['upload_id'];
			$data['create_time'] = $array['create_time'];
			$data['desc'] = $array['desc'];
			$data['ask_uploads'] = $array['ask_uploads'];
			$data['image_url'] = $array['image_url'];
			return $data;
		}
	}