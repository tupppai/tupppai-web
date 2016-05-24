<?php namespace App\Http\Controllers\Main;

use App\Services\User as sUser,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Reply as sReply,
    App\Services\Download as sDownload,
    App\Services\Ask as sAsk,
    App\Services\Category as sCategory,
    App\Services\Thread as sThread;

use App\Models\Reply as mReply,
    App\Models\ThreadCategory as mThreadCategory,
    App\Models\Ask as mAsk;
use Redis;

class CategoryController extends ControllerBase{

    /**
     * 频道列表
     */
    public function index(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);

        $cats = sCategory::getCategories( 'all', 'valid', $page, $size );
        $categories    = [];
        foreach($cats as $key => $category) {
            $categories[] = sCategory::detail( $category );

            if( $category['pid'] == mThreadCategory::CATEGORY_TYPE_ACTIVITY ){
                $categories[$key]['category_type'] = 'activity';
            }
            else if( $category['pid'] == mThreadCategory::CATEGORY_TYPE_CHANNEL ){
                $categories[$key]['category_type'] = 'channel';
            }
            else{
                $categories[$key]['category_type'] = 'nothing';
            }
        }

        return $this->output( $categories );
    }


    public function lists(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);

        $categories = sCategory::getCategories( 'channels', 'valid', $page, $size );
        $data = array();

        foreach($categories as $category) {
            $category = sCategory::detail( $category );

            //获取askid
            $ask = sThreadCategory::getHiddenAskByCategoryId($category['id']);
            $category['ask_id'] = 0;
            if($ask)
                $category['ask_id'] = $ask->id;

            //获取列表
            $threads = sThreadCategory::getRepliesByCategoryId( $category['id'], 1, 2 );
            $category['threads'] = array();
            foreach( $threads as $thread ){
                $category['threads'][] = sThread::parse($thread->target_type, $thread->target_id);
            }

            $data[] = $category;
        }

        return $this->output( $data );
    }


    /**
     * 获取频道详情
     */
    public function show($activity_id) {
        $activity = sCategory::detail( sCategory::getCategoryById( $activity_id ) );

        return $this->output($activity);
    }
    /**
     * 频道下独立数据
     */
    public function channels(){
        $category_id= $this->post('category_id', 'int');
        $type       = $this->post('type', 'string', 'ask');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);

        $data = [];

        if( $type == 'ask' ){
            $threads = sThreadCategory::getRepliesByCategoryId( $category_id, $page, $size  );
            foreach( $threads as $thread ){
                $ask = sAsk::getAskById($thread->id);
                $data[] = sAsk::detail($ask);
            }
        }
        else {
            $asks = sThreadCategory::getCompletedAsksByCategoryId($category_id, $page, $size);
            foreach( $asks as $ask){
                $replies = sReply::getRepliesByAskId($ask->id, 0, 15);

                $ask = sAsk::detail($ask);
                $ask['replies'] = $replies;
                //进行中的用户
                $ask['users']   = sDownload::getAskDownloadedUsers($ask['id'], 0, 15) ;

                $data[] = $ask;
            }
        }
        return $this->output($data);
    }

    /**
     * 活动下的独立数据
     */
    public function activities() {
        $category_id    = $this->post('activity_id', 'int');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $type = $this->post('type', 'string', 'latest');

        if( is_null( $category_id ) || empty( $category_id ) ){
            return error( 'WRONG_ARGUMENTS' );
        }

        if( $category_id == mThreadCategory::CATEGORY_TYPE_GRADUATION ){
            if( $type == 'hot' ){
                $total = Redis::zcard('grad_replies');
                $totalPages = floor($total / $size )+1;
                $page = min( $page, $totalPages );
                $start = ($page-1)*$page;
                $end = min( $start + $size, $total ) ;

                $ids = Redis::zrange('grad_replies', $start, $end );
                $data = [];
                foreach( $ids as $id ){
                    $data[] = sThread::parse( mThreadCategory::TYPE_REPLY, $id );
                }
                return $this->output( $data );
            }
            else if( $type == 'rand' ){
                $allIds = Redis::zrange('grad_replies', 0, -1 );
                $ids = array_rand( $allIds , min( count($allIds), 4) );
                $data = [];
                foreach( $ids as $id ){
                    $data[] = sThread::parse( mThreadCategory::TYPE_REPLY, $allIds[$id] );
                }
                return $this->output( $data );
            }
        }
        $data = array();
        $threads = sThreadCategory::getRepliesByCategoryId( $category_id, $page, $size  );

        foreach( $threads as $thread ){
            $data[] = sThread::parse( $thread->target_type, $thread->target_id);
        }

        return $this->output($data);
    }

}
