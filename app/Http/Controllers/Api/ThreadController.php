<?php namespace App\Http\Controllers\Api;

use App\Models\ModelBase as mModel;
use App\Models\ThreadCategory as mThreadCategory;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;

use App\Services\User as sUser,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\Category as sCategory,
    App\Services\Thread as sThread;

class ThreadController extends ControllerBase{
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

        $tmp = null;
        foreach($threads as $thread) {
            $url = $thread['ask_uploads'][0]['image_url'];
            $width  = $thread['ask_uploads'][0]['image_width'];
            $height = $thread['ask_uploads'][0]['image_height'];
            $thread['ask_uploads'][0]['image_url'] = $thread['image_url'];
            $thread['ask_uploads'][0]['image_width'] = $thread['image_width'];
            $thread['ask_uploads'][0]['image_height'] = $thread['image_height'];
            $thread['image_url'] = $url;
            $thread['image_width'] = $width;
            $thread['image_height'] = $height;

            $tmp = $thread;
            break;
        }
        return $this->output( array($tmp) );
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

    public function get_activitiesAction(){ //new
        $type = $this->post('type', 'string', 'valid');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        $categories = sCategory::getCategoryByPid( mThreadCategory::CATEGORY_TYPE_ACTIVITY, $type );
        $acts = $categories->toArray();
        $activities    = [];
        foreach($acts as $key => $activity) {
            $activities[] = sCategory::detail( $activity );
            $cond = [];
            $cond['category_ids'] = $activity['id'];

            $thread_ids = sThread::getThreadIds( $cond, $page, $size );
            $replies = self::parseAskAndReply( $thread_ids['result'] );
            $activities[$key]['replies'] = $replies;
        }


        return $this->output_json( [
            'activities' => $activities,
            'replies' => []
        ]);
    }

    public function get_worksAction(){
        $cat_id = $this->post('activity_id', 'int');
        $target_type = $this->post('target_type','string', '' );
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        if( is_null( $cat_id ) || empty( $cat_id ) ){
            return error( 'WRONG_ARGUMENTS' );
        }

        $activities = sCategory::getCategoryById( $cat_id );
        $asks = [];
        $replies = [];

        $cond = [];
        $cond['category_ids'] = $cat_id;
        if( $target_type  != 'reply' ){
            $cond['target_type'] = 'ask';
            $threads = sThread::getThreadIds( $cond, $page, $size );
            $asks = self::parseAskAndReply( $threads['result'] );
        }
        if( $target_type != 'ask' ){
            $cond['target_type'] = 'reply';
            $threads = sThread::getThreadIds( $cond, $page, $size );
            $replies = self::parseAskAndReply( $threads['result'] );
        }


        return $this->output_json( [
            'activities' => $activities,
            'asks' => $asks,
            'replies' => $replies
        ]);
    }

    public function get_channelsAction(){
        $cats = sCategory::getCategories( 'channels' );
        $categories = [];
        foreach ($cats as $key => $value) {
            $categories[] = sCategory::detail( $value );
        }

        return $this->output_json( [
            'activities' => [],
            'channels' => $categories,
        ]);
    }

    public function get_threads_by_channelAction(){
        $channel_id = $this->post('channel_id', 'int');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        $cond = [];
        $cond['category_ids'] = $channel_id;

        $thread_ids = sThread::getThreadIds( $cond, $page, $size );
        $replies = self::parseAskAndReply( $thread_ids['result'] );

        return $this->output_json( [
            'replies' => $replies
        ]);
    }

    public static function parseAskAndReply( $ts ){
        //bug 会出现删除的？
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
