<?php namespace App\Http\Controllers\Admin;

use App\Services\ThreadCategory as sThreadCategory;

class IndexController extends ControllerBase
{

    public function indexAction() {
        return $this->output();
    }
    public function aaaAction(){
    	$this->_uid = 999;
    	// $c = sThreadCategory::addCategoryToThread(
    	// 	$this->_uid,
    	// 	2,  /*REPLY*/
    	// 	3,  /*target_id*/
    	// 	'1' /*category_ids*/
    	// );
    	//
        // $c = sThreadCategory::getCategoryIdsByTarget(
        //     1, /* target_type */
        //     385 /* target_id */
        // );
    	//
    	// $c = sThreadCategory::setCategoryOfAsk(
    	// 	$this->_uid,
    	// 	1, /* ask_id */
    	// 	'4,6,3' /* category_ids */
    	// );
    	//
    	// $c = sThreadCategory::setCategoryOfReply(
    	// 	$this->_uid,
    	// 	3, /* reply_id */
    	// 	'9,2' /* category_ids */
    	// );
        //$c = sThreadCategory::getValidThreadsByCategoryId(4);
        $c = sThreadCategory::setThreadStatus( $this->_uid, 1,385, 0, 'asdasd');
    	dd($c);
    }
}


