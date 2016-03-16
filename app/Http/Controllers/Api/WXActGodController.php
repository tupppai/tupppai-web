<?php namespace App\Http\Controllers\Api;

use App\Services\Ask as sAsk;
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

        $this->today_amount = sThreadCategory::countTodaysRequest($this->category->id);
    }

    public function indexAction(){
		return $this->output_json( ['category' => $category ] );
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

        $upload_ids = $this->post( 'upload_ids', 'json_array', array());
        $category_id= $this->category->id;

        $ratios     = $this->post(
            'ratios',
            'json_array',
            config('global.app.DEFAULT_RATIO')
        );
        $scales     = $this->post(
            'scale',
            'json_array',
            config('global.app.DEFAULT_SCALE')
        );
        $desc = $this->post( 'desc', 'string', '' );

        if( !$upload_ids || empty($upload_ids) ) {
            return error('EMPTY_UPLOAD_ID');
        }
        if(!sUpload::getUploadByIds($upload_ids)) {
            return error('EMPTY_UPLOAD_ID');
        }
        if( !$desc ){
			return error('EMPTY_TITLE', '需求不能为空');
        }

        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc, $category_id );

        //更新作品的scale和ratio
        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );

        return $this->output([
            'id' => $ask->id,
            'ask_id' => $ask->id,
            'a' => $this->today_amount
        ]);
    }
}
