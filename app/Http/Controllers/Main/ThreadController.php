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

class ThreadController extends ControllerBase{
   
    /**
     * 好友动态
     */
    public function timeline(){
        $uid = $this->_uid;
        $page = $this->get('page','integer', 1);
        $size = $this->get('size', 'integer', 15);
        $last_updated = $this->get('last_updated', 'integer', time() );

        $threads = sUser::getTimelineThread( $uid, $page, $size, $last_updated );

        return $this->output( $threads );
    }

    /**
     * 关注收藏
     */
    public function subscribed(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $items = sUser::getSubscribed( $uid, $page, $size, $last_updated );

        return $this->output( $items );
    }

    public function search() {
        $uid = $this->_uid;

        $page  = $this->post('page', 'int', 1);           // 页码
        $size  = $this->post('size', 'int', 15);       // 每页显示数量
        $width = $this->post('width', 'int', 480);     // 屏幕宽度
        $desc  = $this->post('desc', 'string');

        $items = sThread::searchThreads($desc, $page, $size);

        return $this->output( $items );
    }

    /**
     * 热门集合
     */
    public function popular() {
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $threads = sThread::getPopularThreads( $uid, $page, $size, $last_updated, 'pc' );
        return $this->output( $threads );
    }

    /**
     * 频道下独立数据
     */
    public function channel(){
        $channel_id = $this->post('channel_id', 'int');
        $type       = $this->post('type', 'string', 'ask');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        $data = [];

        if( $type == 'ask' ){
            $threads = sThreadCategory::getRepliesByCategoryId( $channel_id, $page, $size  );
            foreach( $threads as $thread ){
                $ask = sAsk::getAskById($thread->id);
                $data[] = sAsk::detail($ask);
            }
        }
        else {
            $threads = sThreadCategory::getAsksByCategoryId( $channel_id, mAsk::STATUS_NORMAL, $page, $size );
            foreach( $threads as $thread ){
                $thread->type   = $thread->target_type;
                $thread->id     = $thread->target_id;

                $ask = sAsk::getAskById($thread->id);
                $replies = sReply::getFakeRepliesByAskId($ask->id, 0, 15);

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
     * 频道列表
     */
    public function categories(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);
        $last_updated = $this->get('last_updated','int', time());

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

}
