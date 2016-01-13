<?php namespace App\Http\Controllers\Api;

use App\Models\Label as mLabel,
    App\Models\Reply as mReply,
    App\Models\Message as mMessage;

use App\Services\Count as sCount,
    App\Services\Reply as sReply,
    App\Services\Upload as sUpload,
    App\Services\Label as sLabel,
    App\Services\Message as sMessage,
    App\Services\Collection as sCollection,
    App\Services\Ask as sAsk,
    App\Services\User as sUser;

use App\Jobs\Push;

class ReplyController extends ControllerBase
{
    public $_allow = array('index');
    /**
     * 首页数据
     */
    public function indexAction(){
        //todo: type后续改成数字
        $type   = $this->get( 'type', 'string', 'hot' );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );

        $cond   = array();
        $cond['ask_id'] = $this->get('ask_id', 'int');
        $replies= sReply::getReplies( $cond, $page, $size );

        return $this->output( $replies );
    }

    /**
     * 回复作品
     */
	public function saveAction()
    {
        $ask_id     = $this->post('ask_id', 'int');
		$category_id= $this->post('category_id', 'int');
        $upload_id  = $this->post('upload_id', 'int');
        $ratio      = $this->post("ratio", "float", 0);
        $scale      = $this->post("scale", "float", 0);
        $label_str  = $this->post('labels','json');
        $desc       = $this->post('desc','json');
        $uid        = $this->_uid;

        if( !$upload_id ) {
            return error('EMPTY_UPLOAD_ID');
        }
        if( !$ask_id && !$category_id ) {
            return error('EMPTY_ASK_ID');
        }

        $upload = sUpload::updateImage($upload_id, $scale, $ratio);
        if( $category_id){
            $reply  = sReply::addNewReplyForActivity( $uid, $category_id, $upload_id, $desc );
        }
        else{
            $reply  = sReply::addNewReply( $uid, $ask_id, $upload_id, $desc );
        }
        //$user   = sUser::addUserReplyCount($uid);

        $labels = json_decode($label_str, true);
        $ret_labels = array();
        if (is_array($labels)){
            foreach ($labels as $label) {
                $lbl = sLabel::addNewLabel(
                    $label['content'],
                    $label['x'],
                    $label['y'],
                    $uid,
                    $label['direction'],
                    $upload_id,
                    $reply->id,
                    mLabel::TYPE_REPLY
                );
                $ret_labels[$label['vid']] = array('id'=>$lbl->id);
            }
        }
        //fire('TRADE_HANDLE_REPLY_SAVE',['reply'=>$reply]);
        return $this->output(array(
            'id'=> $reply->id,
            'reply_id'=> $reply->id,
            'labels'=>$ret_labels
        ));
    }

    /**
     * 保存多图作品
     */
    public function multiAction()
    {
        $uid        = $this->_uid;
		$ask_id     = $this->post('ask_id', 'int', 0);
        $category_id= $this->post('category_id', 'int');
        $upload_ids = $this->post('upload_ids', 'json_array' );
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
        $desc       = $this->post( 'desc', 'string', '' );

        if( !$upload_ids || empty($upload_ids) ) {
            return error('EMPTY_UPLOAD_ID');
        }

        //还是单张图片的求助
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_ids[0], $desc, $category_id);

        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );

        return $this->output([
            'id' => $reply->id,
            'ask_id' => $ask_id,
            'category_id' => $category_id
        ]);
    }

    public function deleteAction($id) {
        $status = mReply::STATUS_DELETED;

        $reply  = sReply::getReplyById($id);
        sReply::updateReplyStatus($reply, $status, $this->_uid, "");

        return $this->output();
    }

    public function upReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        sReply::upReply($id, $status);
        return $this->output(['result'=>'ok']);
    }

    public function collectReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        $ret    = sCollection::collectReply($uid, $id, $status);
        return $this->output( ['collection' => $ret] );
    }

    public function informReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        sReply::informReply($id, $status);
        return $this->output();
    }

    public function loveReplyAction($id) {
        $num    = $this->get('num', 'int', 1);
        $status = $this->get('status', 'int', mReply::STATUS_NORMAL);
        $uid    = $this->_uid;

        sReply::loveReply($id, $num, $status);
        return $this->output();
    }
}
