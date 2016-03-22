<?php namespace App\Services;

use App\Models\UserLanding as mUserLanding;
use App\Services\UserLanding as sUserLanding;
use App\Services\WxActGod as sWxActGod;

class Wechat extends ServiceBase
{
	const URL = "http://twww.tupppai.com/";
	public static function godMan($open_id)
	{
		$url = constant('self::URL');
		$user = sUserLanding::getUserByOpenid($open_id,mUserLanding::TYPE_WEIXIN_MP);
		$uid = $user->uid;
		$data = sWxActGod::actGod( $uid );
		if(empty($data)) {
			return "很抱歉暂时无法查询";
		}
		if($data['code'] == -2){
			//被拒绝
			return "很抱歉你还没参加男神活动,<a href=\"{$url}boys/index/index\">点击此处参加</a>";
		}else if($data['code'] == -1){
			//被拒绝
			return '你上传的照片因为'.$data['data']['result']['reason']."无法顺利完成<a href=\"{$url}boys/index/index\">点击重新上传照片</a>";
		}elseif($data['code'] == 1){
			//求P成功且没有作品
			return 'PS爱好者正在小黑屋日夜赶工，喝杯茶再等等吧~';
		}elseif($data['code'] == 2){
			//求P成功且有作品
			return "赶快向你的朋友们炫耀一下你的头像吧~<a href=\"{$data['data']['image']}\">点击查看</a>";
		}
	}

	//关注自动回复
	public static function followAutoReply()
	{
		$url = constant('self::URL');
		return "欢迎关注图派!男神活动正在火热进行中,<a href=\"{$url}boys/index/index\">点击此处参加</a>\n\n已经上传头像的童鞋输入\n“男神”,可以查看进度或者直接领取你的头像作品";
	}
}
