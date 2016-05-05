<?php namespace App\Http\Controllers\Main2;

use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\User as sUser,
    App\Services\Count as sCount,
    App\Services\Focus as sFocus,
    App\Services\Label as sLabel,
    App\Services\ThreadTag as sThreadTag,
    App\Services\Upload as sUpload,
    App\Services\ActionLog as sActionLog,
    App\Services\UserDevice as sUserDevice,
    App\Services\Invitation as sInvitation;

use App\Models\Ask as mAsk;

use App\Counters\AskCounts as cAskCounts;

use Log;

class AskController extends ControllerBase{
    public $_allow = array('index', 'show');
    /**
     * 首页数据
     */
    public function index(){
        $category_id   = $this->get( 'category_id', 'string', 0 );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );

        $cond   = array();
        if( $category_id ){
            $cond['category_id'] = $category_id;
        }
        //todo: add strip_tags
        $asks = sAsk::getAsksByCond( $cond, $page, $size);

        return $this->output( $asks );
    }

    /**
     * 求p详情
     */
    public function show( $ask_id ){
        $page  = $this->get( 'page', 'int', 1 );
        $size  = $this->get(
            'size',
            'int',
            config( 'global.app.DEFAULT_PAGE_SIZE' )
        );
        $width = $this->get(
            'width',
            'int',
            config( 'global.app.DEFAULT_SCREEN_WIDTH' )
        );

        $ask    = sAsk::getAskById($ask_id);
        if(!$ask)
            return error('ASK_NOT_EXIST');

        $ask    = sAsk::detail( $ask );
        $asker  = sUser::getUserByUid( $ask['uid'] );

        // 如果传入reply_id参数，则置顶该id
        $reply_id = $this->get('reply_id', 'int');
        if( $reply_id ) {
            $replies = sReply::getAskRepliesWithOutReplyId( $ask_id, $reply_id, $page, $size );
        }
        else {
            $replies = sReply::getRepliesByAskId( $ask_id, $page, $size );
        }

        //将第一个作品塞到列表里面
        if( $reply_id && $page == 1 ){
            $reply = sReply::getReplyById($reply_id);
            if($reply && $reply->ask_id == $ask_id) {
                $reply = sReply::detail($reply);
                array_unshift($replies, $reply);
            }
        }

        $data = array();
        if( $page == 1 ){
            $ask['sex'] = $asker['sex']?1:0;
            $ask['avatar'] = $asker['avatar'];
            $ask['nickname'] = $asker['nickname'];
            $data['ask'] = $ask;
        }

        $data['replies'] = $replies;

        cAskCounts::inc($ask_id,'click');

        return $this->output( $data );
    }

    /**
     * 保存求p
     */
    public function save()
    {
        $upload_id  = $this->post( 'upload_id', 'string' );
        $desc       = $this->post( 'desc', 'string', '' );
        $label_str  = $this->post( 'labels', 'json' );
        $ratio      = $this->post(
            'ratio',
            'float',
            config('global.app.DEFAULT_RATIO')
        );
        $scale      = $this->post(
            'scale',
            'float',
            config('global.app.DEFAULT_SCALE')
        );

        if( !$upload_id ) {
            return error('EMPTY_UPLOAD_ID');
        }

        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc );
        $upload = sUpload::updateImage( $upload_id, $scale, $ratio );

        $labels     = json_decode($label_str, true);
        $ret_labels = array();
        if( is_array( $labels ) ){
            foreach( $labels as $label ){
                $lbl = sLabel::addNewLabel(
                    $label['content'],
                    $label['x'],
                    $label['y'],
                    $this->_uid,
                    $label['direction'],
                    $upload_ids,
                    $ask->id
                );
                $ret_labels[ $label['vid'] ] = ['id' => $lbl->id];
            }
        }

        //新建求P触发事件
        fire('TRADE_HANDLE_ASKS_SAVE',['ask'=>$ask]);
        //listen('TRADE_HANDLE_ASKS_SAVE',['ask'=>$ask]);
        return $this->output([
            'id' => $ask->id,
            'ask_id' => $ask->id,
            'labels' => $ret_labels
        ]);
    }

    /**
     * 保存多图求p
     */
    public function multiAction()
    {
        $upload_ids = $this->post( 'upload_ids', 'json_array', array());
        $tag_ids    = $this->post( 'tag_ids', 'json_array', array());
        $category_id= $this->post( 'category_id', 'int', NULL );

        $ratios     = $this->post(
            'ratios',
            'json_array',
            config('global.app.DEFAULT_RATIO')
        );
        $scales     = $this->post(
            'scale',
            'json_array',
            config('global.app.DEFAULT_SCALE')
        );
        $desc = $this->post( 'desc', 'string', '' );

        if( !$upload_ids || empty($upload_ids) ) {
            return error('EMPTY_UPLOAD_ID');
        }
        if(!sUpload::getUploadByIds($upload_ids)) {
            return error('EMPTY_UPLOAD_ID');
        }

        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc, $category_id );

        //更新作品的scale和ratio
        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );
        //保存标签，由于是发布求助，因此可以直接add
        foreach($tag_ids as $tag_id) {
            sThreadTag::addTagToThread( $this->_uid, mAsk::TYPE_ASK, $ask->id, $tag_id );
        }

        fire('TRADE_HANDLE_ASKS_SAVE',['ask'=>$ask]);
        return $this->output([
            'id' => $ask->id,
            'ask_id' => $ask->id
        ]);
    }

    public function editAction() {
        $ask_id = $this->post('ask_id', 'int');
        $desc   = $this->post('desc', 'string');

        sAsk::updateAskDesc($ask_id, $desc);
        return $this->output();
    }

    public function upAskAction( $id ) {
        $status = $this->get( 'status', 'int', mAsk::STATUS_NORMAL );

        sAsk::upAsk($id, $status);
        return $this->output();
    }

    public function deleteAction($id) {
        $status = mAsk::STATUS_DELETED;

        $ask = sAsk::getAskById($id);
        sAsk::updateAskStatus($ask, $status, $this->_uid, "");

        return $this->output();
    }

    public function informAskAction( $id ) {
        $status = $this->get( 'status', 'int', mAsk::STATUS_NORMAL );

        sAsk::informAsk($id, $status);
        return $this->output();
    }

    public function focusAskAction($id) {
        $status = $this->get( 'status', 'int', mAsk::STATUS_NORMAL );
        $uid    = $this->_uid;

        $ret    = sFocus::focusAsk( $uid, $id, $status );
        return $this->output( $ret );
    }
}

