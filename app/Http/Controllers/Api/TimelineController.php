<?php namespace App\Http\Controllers\Api;

use App\Services\Reply as sReply;
use App\Services\Upload as sUpload;
use App\Models\Reply as mReply;
class TimelineController extends ControllerBase{

    public function showAction( $id ){
        $replies= sReply::getReplyById(  $id );

        return $this->output( sReply::detail( $replies ) );
    }

    /**
     * 保存多图作品
     */
    public function multiAction()
    {
        $uid        = $this->_uid;
		$ask_id     = 0;
        $category_id= mReply::CATEGORY_TYPE_TIMELINE;
        $upload_ids = $this->post('upload_ids', 'json_array' );
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
        $desc       = $this->post( 'desc', 'string' );

        if( !$upload_ids || empty($upload_ids) ) {
            return error('EMPTY_UPLOAD_ID');
        }
        if( $desc == '' ){
			return error('EMPTY_CONTENT', '描述内容不能为空');
        }

        //还是单张图片的求助
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_ids[0], $desc, $category_id);

        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );

        return $this->output([
            'id' => $reply->id,
            'ask_id' => $ask_id,
            'category_id' => $category_id
        ]);
    }

    public function loveReplyAction($id) {
        $num    = $this->get('num', 'int', 1);
        $status = $this->get('status', 'int', mReply::STATUS_NORMAL);
        $uid    = $this->_uid;

        sReply::loveReply($id, $num, $status);
        return $this->output();
    }
}
