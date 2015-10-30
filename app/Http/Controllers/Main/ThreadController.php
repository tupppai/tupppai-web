<?php namespace App\Http\Controllers\Main;

use App\Services\User as sUser,
    App\Services\Thread as sThread;

class ThreadController extends ControllerBase{
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
    public function subscribedAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $items = sUser::getSubscribed( $uid, $page, $width, $last_updated );

        return $this->output( $items );
    }
}
