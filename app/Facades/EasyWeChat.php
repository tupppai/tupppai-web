<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EasyWeChat extends Facade
{
	protected static function getFacadeAccessor()
	{
		return "EasyWeChat";
	}
}