<?php namespace App\Formats;


class Tags extends FormatBase
{
	public static function UserHistoryForTag($history)
	{
		if(empty($history)){
			return [];
		}
		$data['tag_id']  = $history->tag->id;
		$data['name'] 	 = $history->tag->name;
		return $data;
	}
}