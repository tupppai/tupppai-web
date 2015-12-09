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

class ThreadController extends ControllerBase{
    public function homeAction(){
        // $type = $this->post('type', 'string', 'valid');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);
        $last_updated = $this->get('last_updated','int', time());

        $cats = sCategory::getCategories( 'all', 'valid', $page, $size );
        $categories    = [];
        foreach($cats as $key => $category) {
            $categories[] = sCategory::detail( $category );

            $threads = sThreadCategory::getRepliesByCategoryId( $category['id'], 1, 5 );
            foreach( $threads as $thread ){
                $thread->type = $thread->target_type;
                $thread->id = $thread->target_id;
            }
            $replies = self::parseAskAndReply( $threads );

            $categories[$key]['threads'] = $replies;

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


        return $this->output_json( [
            'categories' => $categories
        ]);
    }

    public function itemAction() {
        $type = $this->get('type', 'int', mModel::TYPE_ASK);
        $id   = $this->get('id', 'int');

        if(!$id) {
            return error('EMPTY_ID');
        }

        if($type == mModel::TYPE_ASK) {
            $model = sAsk::brief(sAsk::getAskById($id));
        }
        else {
            $model = sReply::brief(sReply::getReplyById($id));
        }
        return $this->output( $model );
    }

    /**
     * 热门集合
     */
    public function popularAction() {
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $threads = sThread::getPopularThreads( $uid, $page, $size, $last_updated, 'app' );
        return $this->output( $threads );
    }

    public function activitiesAction(){ //old
        $uid = $this->_uid;
        $type = $this->post('type', 'string', 'valid');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        //目前只有一个活动
        $activities = sAsk::getActivities( $type, 0, 1  );

        $replies    = array();
        foreach($activities as $activity) {
            $replies = array_merge($replies, sReply::getRepliesByAskId( $activity['ask_id'], $page, $size ));
        }

        return $this->output_json( [
            'activities' => $activities,
            'replies' => $replies
        ]);
    }

    //返回所有活动及其对应作品(仅活动！！)  貌似这个接口没有用了，用home
    public function get_activitiesAction(){ //new
        $type = $this->post('type', 'string', 'valid');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 5);
        $last_updated = $this->get('last_updated','int', time());

        $categories = sCategory::getCategoryByPid( mThreadCategory::CATEGORY_TYPE_ACTIVITY, $type, $page, $size );
        $acts = $categories->toArray();
        $activities    = [];
        foreach($acts as $key => $activity) {
            $activities[] = sCategory::detail( $activity );
            $cond = [];
            $cond['category_ids'] = $activity['id'];

            //作品默认拉5个
            $thread_ids = sThread::getThreadIds( $cond, 1, 5 );
            $threads = self::parseAskAndReply( $thread_ids['result'] );

            /*
            $categories = sThreadCategory::getThreadsByCategoryId($activity['id']);
            foreach($categories as $category) {
                if($category->target_type == mThreadCategory::TYPE_ASK) {
                    $activities[$key]['ask_id'] = $category->target_id;
                    break;
                }
            }
             */
            $activities[$key]['threads']  = $threads;
        }

        return $this->output_json( [
            'activities' => $activities
        ]);
    }

    public function get_activity_threadsAction(){
        $cat_id = $this->post('activity_id', 'int');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        if( is_null( $cat_id ) || empty( $cat_id ) ){
            return error( 'WRONG_ARGUMENTS' );
        }

        $cond = [];
        $cond['category_ids'] = $cat_id;
        $cond['target_type'] = 'reply';

        $reps = sThread::getThreadIds( $cond, $page, $size );
        $replies = [];
        foreach( $reps['result'] as $reply ){
            $replies[] = sReply::detail( sReply::getReplyById( $reply->id) );
        }

        $ask_id = 0;
        $categories = sThreadCategory::getThreadsByCategoryId($cat_id);
        foreach($categories as $category) {
            if($category->target_type == mThreadCategory::TYPE_ASK) {
                $ask_id = $category->target_id;
                break;
            }
        }
        $activity = sCategory::detail( sCategory::getCategoryById( $cat_id ) );


        return $this->output_json( [
            'activity' => $activity,
            'ask_id'=>$ask_id,
            'replies' => $replies
        ]);
    }

    public function get_threads_by_channelAction(){
        $channel_id = $this->post('channel_id', 'int');
        $target_type = $this->post('target_type', 'string', '');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        $asks = [];
        $replies = [];

        $cond = [];
        if( $target_type  != 'reply' ){
            $threads = sThreadCategory::getAsksByCategoryId( $channel_id, mThreadCategory::STATUS_NORMAL, $page, $size );
            foreach( $threads as $thread ){
                $thread->type = $thread->target_type;
                $thread->id = $thread->target_id;
            }
            $asks = self::parseAskAndReply( $threads );
        }
        if( $target_type != 'ask' ){
            $cond['target_type'] = 'reply';
            $threads = sThreadCategory::getRepliesByCategoryId( $channel_id, $page, $size  );
            foreach( $threads as $thread ){
                $thread->type = $thread->target_type;
                $thread->id = $thread->target_id;
            }
            $replies = self::parseAskAndReply( $threads );
        }

        //$thread_ids = sThread::getThreadIds( $cond, $page, $size );
        //$replies = self::parseAskAndReply( $thread_ids['result'] );

        return $this->output_json( [
            'ask' => $asks,
            'replies' => $replies
        ]);
    }

    public static function parseAskAndReply( $ts ){
        $threads = array();
        foreach( $ts as $key=>$value ){
            switch( $value->type ){
            case mReply::TYPE_REPLY:
                $reply = sReply::getReplyById($value->id) ;
                if(!$reply) continue;
                $reply = sReply::detail( $reply );
                array_push( $threads, $reply );
                break;
            case mAsk::TYPE_ASK:
                $ask = sAsk::getAskById( $value->id );
                if(!$ask) continue;
                $ask = sAsk::detail( $ask );
                array_push( $threads, $ask );
                break;
            }
        }

        return $threads;
    }

    /**
     * 好友动态
     */
    public function timelineAction(){
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
    public function subscribedAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $items = sUser::getSubscribed( $uid, $page, $size, $last_updated );

        return $this->output( $items );
    }

    public function searchAction() {
        $uid = $this->_uid;

        $page  = $this->post('page', 'int', 1);           // 页码
        $size  = $this->post('size', 'int', 15);       // 每页显示数量
        $width = $this->post('width', 'int', 480);     // 屏幕宽度
        $desc  = $this->post('desc', 'string');

        $items = sThread::searchThreads($desc, $page, $size);

        return $this->output( $items );
    }
}
