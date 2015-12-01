<?php namespace App\Http\Controllers\Api;

use App\Models\ModelBase as mModel;

use App\Services\User as sUser,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
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
        $uid = $this->_uid;
        $type = $this->post('type', 'string', 'valid');
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $last_updated = $this->get('last_updated','int', time());

        switch( $type ){
            case 'valid':
                $status = mThreadCategory::STATUS_NORMAL;
                break;
            case 'done':
                $status = mThreadCategory::STATUS_DONE;
                break;
            case 'next':  //即将开始的活动（公开的）
                $status = mThreadCategory::STATUS_READY;
                break;
            case 'hidden':
            case 'ready': //后台储备的
                $status = mThreadCategory::STATUS_HIDDEN;
                break;
            case 'all':
            default:
                $status = [
                    mThreadCategory::STATUS_NORMAL,
                    mThreadCategory::STATUS_READY,
                    mThreadCategory::STATUS_DONE
                ];
                break;
        }
        }
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
