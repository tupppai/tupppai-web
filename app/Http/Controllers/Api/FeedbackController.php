<?php namespace App\Http\Controllers\Api;

use App\Services\Feedback as sFeedback;
use App\Services\User as sUser;

class FeedbackController extends ControllerBase {
	public function saveAction(){
        $uid = $this->_uid;

		$content = $this->post('content', 'string');
		if( empty( $content ) ){
			return error( 'EMPTY_CONTENT', '反馈内容不能为空');
        }

		$contact = $this->post('contact', 'string');
        if( empty( $contact ) ){
            $contact = sUser::getPhoneByUid($uid);
        }

		$ret = sFeedback::addNewFeedback( $uid, $content, $contact );
		return $this->output( $ret );
	}

}
