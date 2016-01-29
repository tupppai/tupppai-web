<?php namespace App\Http\Controllers\Api;

use App\Models\ModelBase as mModel;
use App\Models\ThreadCategory as mThreadCategory;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Comment as mComment;

use App\Services\Reward as sReward;
use App\Services\User as sUser,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\Comment as sComment,
    App\Services\Category as sCategory,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Thread as sThread;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
use Illuminate\Support\Facades\DB;
use Log;

class ThreadController extends ControllerBase{
    public $_allow = [
        'tutorial_details',
        'popular'
    ];

    public function itemAction() {
        $type = $this->get('type', 'int', mModel::TYPE_ASK);
        $id   = $this->get('id', 'int');

        if(!$id) {
            return error('EMPTY_ID');
        }
        $item = sThread::parse($type, $id);

        return $this->output( $item );
    }

    /**
     * 热门集合
     */
    public function popularAction() {
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);        // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度

        $threads = sThreadCategory::getPopularThreads( 'app', $page, $size );
        return $this->output( $threads );
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


    public function tutorials_listAction(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);

        $tutorials = sThreadCategory::getAsksByCategoryId( mThreadCategory::CATEGORY_TYPE_TUTORIAL, mThreadCategory::STATUS_NORMAL, $page, $size, mAsk::STATUS_NORMAL );

        $pc_host = env('MAIN_HOST');
        $data = array();
        foreach($tutorials as $tutorial) {
            $tutorial = sAsk::tutorialDetail( sAsk::getAskById( $tutorial->target_id ) );
            $tutorial['ask_uploads'] = [];//[array_pop( $tutorial['ask_uploads'] )];
            $data[] = $tutorial;
        }

        return $this->output([
            'tutorials' => $data
        ]);
    }

    public function tutorial_detailsAction(){
        $ask_id = $this->get('tutorial_id', 'int');
        $ask    = sAsk::getAskById($ask_id, 0);
        if(!$ask){
            return error('ASK_NOT_EXIST');
        }

        $ask    = sAsk::tutorialDetail( $ask );
        return $this->output( $ask );
    }

    //准备删掉====================================================
    public function activitiesAction(){ //old
        $uid = $this->_uid;
        $activity_id = $this->post('activity_id', 'int');
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

        return $this->output( [
            'activities' => $activities,
            'replies' => $replies
        ]);
    }

    //返回所有活动及其对应作品(仅活动！！)  貌似这个接口没有用了，用home
    public function get_activitiesAction(){ //new
        $type = $this->post('type', 'string', 'valid');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 5);

        $activities = sCategory::getCategoryByPid( mThreadCategory::CATEGORY_TYPE_ACTIVITY, $type, $page, $size );

        $data = array();
        foreach($activities as $activity) {
            $thread_ids = sThread::getThreadIds( array($activity->id), 1, 5 );
            $threads    = array();
            foreach($thread_ids['result'] as $row) {
                $threads[] = sThread::parse($row->type, $row->id);
            }
            $activity   = sCategory::detail($activity);
            $activity['threads'] = $threads;

            $data[] = $activity;
        }

        return $this->output([
            'activities' => $data
        ]);
    }
    public function get_activity_threadsAction(){
        $cat_id = $this->post('activity_id', 'int');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);

        if( is_null( $cat_id ) || empty( $cat_id ) ){
            return error( 'WRONG_ARGUMENTS' );
        }

        $cond = [];
        $cond['category_ids'] = $cat_id;
        $cond['target_type'] = 'reply';

        $reps = sThreadCategory::getRepliesByCategoryId( $cat_id, $page, $size  );
        $replies = [];
        foreach($reps as $reply) {
            $replies[] = sReply::detail( sReply::getReplyById( $reply->target_id) );
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
        $activity['ask_id'] = $ask_id;

        return $this->output( [
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

        $asks = [];
        $replies = [];

        $cond = [];
        if( $target_type  != 'reply' ){
            $threads = sThreadCategory::getAsksByCategoryId( $channel_id, array(
                mThreadCategory::STATUS_NORMAL,
                mThreadCategory::STATUS_DONE
            ), $page, $size );
            $asks = [];
            foreach( $threads as $thread ){
                $asks[] = sAsk::detail( sAsk::getAskById( $thread->target_id ) );
            }
        }
        if( $target_type != 'ask' ){
            $cond['target_type'] = 'reply';
            $threads = sThreadCategory::getRepliesByCategoryId( $channel_id, $page, $size  );
            $replies = [];
            foreach( $threads as $thread ){
                $replies[] = sReply::detail( sReply::getReplyById( $thread->target_id ) );
            }
        }

        return $this->output( [
            'ask' => $asks,
            'replies' => $replies
        ]);
    }

    public function homeAction(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);

        $categories = sCategory::getCategories( 'all', 'valid', $page, $size );
        $data = array();

        foreach($categories as $category) {
            $category = sCategory::detail( $category );

            //获取askid
            $ask_id = 0;
            $threads = sThreadCategory::getAsksByCategoryId( $category['id'], array(
                mThreadCategory::STATUS_HIDDEN
            ), 1, 999);
            foreach($threads as $thread) {
                $ask_id = $thread->target_id;
                break;
            }
            $category['ask_id'] = $ask_id;

            //获取列表
            $threads = sThreadCategory::getRepliesByCategoryId( $category['id'], 1, 5 );
            $category['threads'] = array();
            foreach( $threads as $thread ){
                $category['threads'][] = sThread::parse($thread->target_type, $thread->target_id);
            }

            //获取频道类型
            if( $category['pid'] == mThreadCategory::CATEGORY_TYPE_ACTIVITY ) {
                $category['category_type'] = 'activity';
            }
            else if( $category['pid'] == mThreadCategory::CATEGORY_TYPE_CHANNEL ) {
                $category['category_type'] = 'channel';
            }
            else if( $category['pid'] == mThreadCategory::CATEGORY_TYPE_TUTORIAL ){
                $category['category_type'] = 'tutorial';
            }
            else {
                $category['category_type'] = 'nothing';
            }

            $data[] = $category;
        }

        return $this->output( [
            'categories' => $data
        ]);
    }

    public function rewardAction()
    {
        $uid    = $this->_uid;
        $ask_id = $this->get( 'ask_id', 'int', null);
        $amount = $this->get( 'amount', 'int', null);
        if(empty($ask_id) || empty($uid)){
            error('EMPTY_ARGUMENTS');
        }
        //生成随机打赏金额
        $amount = $amount ? $amount : randomFloat(config('global.reward_amount_scope_start'), config('global.reward_amount_scope_end'));
        //打赏
        $reward = sReward::createReward($uid, $ask_id ,$amount);

        $type = sReward::STATUS_NORMAL;

        if(!$reward) {
            $type = sReward::STATUS_FAILED;
        }
        $balance = sUser::getUserBalance($uid);

        return $this->output([
            'amount' => money_convert($amount),
            'type' => $type,
            'balance' => $balance
        ]);
    }
    public function rewardCountAction()
    {
        $uid = $this->_uid;
        $ask_id = $this->get( 'ask_id', 'int', null);
        //已达打赏过次数
        $count= sReward::getAskRewardCount( $uid , $ask_id );
        return $count;
    }

}
