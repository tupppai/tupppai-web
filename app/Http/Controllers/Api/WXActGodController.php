<?php namespace App\Http\Controllers\Api;

use App\Services\WX as sWX;
use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Upload as sUpload;
use App\Services\Category as sCategory;
use App\Services\ThreadCategory as sThreadCategory;
use App\Models\ThreadCategory as mThreadCategory;
use Request;

class WXActGodController extends ControllerBase{
	protected $category;
	protected $today_amount = 0;

	const MAX_REQUEST_PER_DAY = 200;
    const ASSIGN_RECORD_META_NAME = 'WXActGod_assign_records';
    const ASSIGN_UID_META_NAME = 'WXActGod_assign_uid';
    public function __construct( Request $request ){
        parent::__construct( $request );

        $category = sCategory::getCategoryByName('WXActGod');
        if(!$category){
            return error('CATEGORY_NOT_EXIST', '活动不存在');
        }

        $this->category = $category;

        $this->total_amount = sThreadCategory::countTotalRequests($this->category->id);
        $this->today_amount = sThreadCategory::countTodaysRequest($this->category->id);
        $this->left_amount = sThreadCategory::countLeftRequests($this->category->id);
    }

    public function indexAction(){
        $min_requested_people = 5;
        $user_amounts = sUser::countUserAmount();
        $rand_users = array_rand( range(1, $user_amounts), $min_requested_people);
        $avatars = [];
        foreach( $rand_users as $uid){
            $avatars[] = sUser::getUserAvatarByUid( $uid );
        }
		return $this->output_json( [
            'category' => $this->category,
            'today_amount' => $this->today_amount,
            'left_amount' => $this->left_amount,
            'total_amount' => $this->total_amount +$min_requested_people,
            'avatars' => $avatars
        ] );
    }

	/**
     * 保存多图求p
     */
    public function multiAction()
    {

		if( $this->category->end_time < time( ) ){
			return error('ACTIVITY_ENDED', '活动已结束');
		}
		if( $this->today_amount > self::MAX_REQUEST_PER_DAY ){
			return error('MAX_REQUEST_PER_DAY_EXCEEDED', '已达每日上限');
        }

        $media_id = $this->get('media_id', 'int', 0);
        $upload_ids = sWX::getUploadId( $media_id );
        if( is_null($upload_ids)){
            return error('WRONG_ARGUMENTS', 'token获取失败');
        }
        if( $upload_ids === 0 ){
            return error('WRONG_ARGUMENTS', '获取图片失败');
        }
        if( $upload_ids === -1 ){
            return error('WRONG_ARGUMENTS', '保存图片失败');
        }
        if(!sUpload::getUploadByIds($upload_ids)) {
            return error('EMPTY_UPLOAD_ID');
        }

        $category_id= $this->category->id;
        $desc = $this->post( 'desc', 'string', '' );

        if( !$desc ){
			return error('EMPTY_TITLE', '需求不能为空');
        }

        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc, $category_id );

        return $this->output([
            'id' => $ask->id,
            'ask_id' => $ask->id,
            'today' => $this->today_amount,
            'left' => $this->left_amount
        ]);
    }
}
