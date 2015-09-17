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
    	//$c = sThreadCategory::setCategoryOfThread(
    	//	$this->_uid,
    	//	1, /* thread_categories_id */
    	//	'4,2,42' /* category_ids */
    	//);
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
    	dd($c);
    }
}

