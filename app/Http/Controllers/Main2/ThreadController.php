<?php namespace App\Http\Controllers\Main2;

use App\Services\User as sUser,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Reply as sReply,
    App\Services\Download as sDownload,
    App\Services\Ask as sAsk,
    App\Services\Banner as sBanner,
    App\Services\Tag as sTag,
    App\Services\Comment as sComment,
    App\Services\Category as sCategory,
    App\Services\Reward as sReward,
    App\Services\Thread as sThread;
use App\Services\UserLanding as sUserLanding;

use App\Models\Reply as mReply,
    App\Models\ThreadCategory as mThreadCategory,
    App\Models\Tag as mTag,
    App\Models\Reward as mReward,
    App\Models\Ask as mAsk;
use App\Models\UserLanding as mUserLanding;

use App\Counters\AskCounts as cAskCounts;
use App\Counters\ReplyCounts as cReplyCounts;
use App\Trades\User as tUser;
use App\Trades\Account as tAccount;
use App\Trades\Transaction as tTransaction;

use PingppLog;
use DB;


class ThreadController extends ControllerBase{
    public $_allow = '*';
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

        $threads = sThreadCategory::getPopularThreadsV2( 'pc', $page, $size );
        $data    = [];
        foreach($threads as $thread) {
            $data[] = sThreadCategory::brief($thread);
        }
        return $this->output( $data );
    }

    /**
     * 频道列表
     */
    public function categories(){
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

    /**
     * 获取ask/reply以及其相关的评论
     * @param  integer $type 类型，ask或reply
     * @param  integer $id   id
     */
    public function view($type, $id)
    {
        //todo:判断类型，对应的检出
        switch ($type) {
            case mAsk::TYPE_ASK:
                //todo:抓出ask
                $ask = sAsk::getAskById($id);
                $data = sAsk::detailV2($ask, null, 999);
                break;
            case mReply::TYPE_REPLY:
                //todo:抓出reply
                $reply = sReply::getReplyById($id);
                $data = sReply::detailV2($reply, null, 999);
                break;
            default:
                return error('WRONG_ARGUMENTS');
                break;
        }
        return $this->output($data);
    }

    public function getBannerAndTags(){
        //get banner
        $banner_objects = sBanner::getBanners();
        $banners = [];
        foreach( $banner_objects as $banner ){
            $banners[] = sBanner::detail( $banner );
        }

        //get tag
        $tags = sTag::getTagsByCond(['status' => mTag::STATUS_DONE], 1, 4 );

        //merge
        $data = [];
        $data['banners'] = $banners;
        $data['tags'] = $tags;

        return $this->output( $data );
    }

    public function reward(){
        $uid    = $this->_uid;
        $amount = $this->post( 'amount', 'money', null);
        $target_id   = $this->post( 'target_id', 'int', null);
        $target_type = $this->post('target_type', 'int');
        $comment = $this->post('comment', 'string');
        $pay_type= $this->post('payment_type', 'string', 'wx_pub');
        $user_landing = sUserLanding::getUserLandingByUid( $uid, mUserLanding::TYPE_WEIXIN_MP );
        if( !$user_landing ){
            return error('USER_LANDING_NOT_EXIST', '没有openid');
        }
        $open_id = $user_landing->openid;


        if(empty($target_id) || empty($uid)||empty($target_type)||empty($comment)||empty($amount)){
            return error('EMPTY_ARGUMENTS');
        }

        $charge   = tAccount::pay($this->_uid, $amount, $pay_type, [
            'type'=>'reward',
            'target_type' => $target_type,
            'target_id'=>$target_id,
            'open_id' => $open_id,
            'comment' => $comment
        ]);

        return $this->output(['charge'=>$charge]);
    }
}
