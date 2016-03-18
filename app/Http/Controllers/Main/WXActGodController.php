<?php namespace App\Http\Controllers\Main;

use App\Services\Wx as sWX;
use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Reply as sReply;
use App\Services\Upload as sUpload;
use App\Services\Askmeta as sAskmeta;
use App\Services\Category as sCategory;
use App\Services\ThreadCategory as sThreadCategory;
use App\Models\ThreadCategory as mThreadCategory;
use App\Services\WxActGod;
use Request;

class WXActGodController extends ControllerBase{
	protected $category;
	protected $today_amount = 0;
    protected $ask = NULL;
    protected $reply = NULL;

	const MAX_REQUEST_PER_DAY = 200;
    const ASSIGN_RECORD_META_NAME = 'WXActGod_assign_records';
    const ASSIGN_UID_META_NAME = 'WXActGod_assign_uid';
    protected $godNames = [
        '胡歌',
        '王俊凯',
        '易烊千玺',
        '王源',
        '宋仲基',
        '鹿晗',
        '吴亦凡'
    ];
    protected $affectNames = [
        '双重曝光',
        '彩绘',
        '欧美风'
    ];

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

        $thcat = sThreadCategory::getAsksByCategoryId( $category->id, [mThreadCategory::STATUS_NORMAL], 1, 1, [ mThreadCategory::STATUS_REJECT, mThreadCategory::STATUS_DONE ], $this->_uid );

        if( !$thcat->isEmpty() ){
            $thcat = $thcat[0];
            $this->ask = sAsk::getAskById( $thcat->target_id );
        }
    }

    public function index()
    {
        $data = WxActGod::actGod();

        return $this->output($data);
    }

    public function avatars()
    {
        $avatars = WxActGod::avatars();
        return $this->output(['avatars' => $avatars]);
    }

    public function rand()
    {
        if (session('god_rand')) {
            $rand = session('god_rand');
        } else {
            $rand = rand(0, 2);
            session(['god_rand' => $rand]);
        }
        return $this->output(['rand' => $rand]);
    }
	/**
     * 保存多图求p
     */
    public function multi()
    {

		if( $this->category->end_time < time( ) ){
			return error('ACTIVITY_ENDED', '活动已结束');
		}
		if( $this->today_amount > self::MAX_REQUEST_PER_DAY ){
			return error('MAX_REQUEST_PER_DAY_EXCEEDED', '已达每日上限');
        }

        $media_id = $this->get('media_id', 'string', 0);
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
        if(!sUpload::getUploadById($upload_ids)) {
            return error('EMPTY_UPLOAD_ID');
        }

        $category_id= $this->category->id;
        $req = $this->post( 'desc', 'string', '' );

        if( !$req ){
			return error('EMPTY_TITLE', '需求不能为空');
        }

        $d = explode( '-', $req );
        if( count( $d )!=2){
            return error('WRONG_ARGUMENTS','参数不足');
        }
        $name = $this->godNames[$d[0]%7];
        $affect = $this->affectNames[$d[1]%3];

        $desc = $name.'的'.$affect.'效果('.$req.')';
        $ask    = sAsk::addNewAsk( $this->_uid, [$upload_ids], $desc, $category_id );

        return $this->output([
            'result' => 'ok',
            'id' => $ask->id,
            'ask_id' => $ask->id,
            'today' => $this->today_amount,
            'left' => $this->left_amount
        ]);
    }
}
