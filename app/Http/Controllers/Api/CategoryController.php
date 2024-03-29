<?php namespace App\Http\Controllers\Api;

use App\Models\ModelBase as mModel;
use App\Models\ThreadCategory as mThreadCategory;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;

use App\Services\User as sUser,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\Category as sCategory,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Thread as sThread;

class CategoryController extends ControllerBase{
    public $_allow = '*';

    public function indexAction(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);

        $categories = sCategory::getCategories( 'all', 'valid', $page, $size );
        $data = array();

        foreach($categories as $category) {
            $category = sCategory::detail( $category );

            //获取askid
            $ask = sThreadCategory::getHiddenAskByCategoryId($category['id']);
            $category['ask_id'] = 0;
            if($ask)
                $category['ask_id'] = $ask->id;

            //获取列表
            $threads = sThreadCategory::getRepliesByCategoryId( $category['id'], 1, 5 );
            $category['threads'] = array();
            foreach( $threads as $thread ){
                $category['threads'][] = sThread::parse($thread->target_type, $thread->target_id);
            }

            $data[] = $category;
        }

        return $this->output( $data );
    }

    /**
     * 通过活动/频道id获取集合数据
     */
    public function threadsAction() {
        $category_id    = $this->post('category_id', 'int');
        $type           = $this->post('type', 'string');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);

        if( is_null( $category_id ) || empty( $category_id ) ){
            return error( 'WRONG_ARGUMENTS' );
        }

        $data = array();
        if( $type == 'ask' ){
            $threads = sThreadCategory::getAsksByCategoryId( $category_id, array(
                mThreadCategory::STATUS_NORMAL,
                mThreadCategory::STATUS_DONE
            ), $page, $size );
        }
        else if( $type == 'reply' ){
            $threads = sThreadCategory::getRepliesByCategoryId( $category_id, $page, $size  );
        }

        foreach( $threads as $thread ){
            $data[] = sThread::parse( $thread->target_type, $thread->target_id);
        }

        return $this->output($data);
    }

    public function viewAction(){
        $cat_id = $this->get('category_id', 'int');
        if( !$cat_id ){
            return error('EMPTY_CATEGORY_ID','分类id不能为空');
        }

        $ask_id = 0;
        $threads = sThreadCategory::getAsksByCategoryId( $cat_id, array(
            mThreadCategory::STATUS_HIDDEN
        ), 1, 999);
        foreach($threads as $thread) {
            $ask_id = $thread->target_id;
            break;
        }

        $activity = sCategory::detail( sCategory::getCategoryById( $cat_id ) );
        //获取askid
        $ask = sThreadCategory::getHiddenAskByCategoryId($cat_id);
        $activity['ask_id'] = 0;
        if($ask)
            $activity['ask_id'] = $ask->id;

        return $this->output([
            'activity' => $activity,
        ]);
    }
}
