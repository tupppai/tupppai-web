<?php namespace App\Http\Controllers\Android;

use App\Services\User as sUser,
    App\Services\Thread as sThread;

class ThreadController extends ControllerBase{
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

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $desc  = $this->get('desc', 'string');

        $items = sThread::searchThreads($desc, $page, $size);

        return $this->output( $items );
    }
}
