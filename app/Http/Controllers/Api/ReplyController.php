<?php namespace App\Http\Controllers\Api;

use App\Models\Label as mLabel,
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
        $upload_id  = $this->post('upload_id', 'int');
        $ratio      = $this->post("ratio", "float", 0);
        $scale      = $this->post("scale", "float", 0);
        $label_str  = $this->post('labels','json');
        $desc       = $this->post('desc','json');
        $uid        = $this->_uid;

        if( !$upload_id ) {
            return error('EMPTY_UPLOAD_ID');
        }
        if( !$ask_id ) {
            return error('EMPTY_ASK_ID');
        }

        $upload = sUpload::updateImage($upload_id, $scale, $ratio);
        $ask    = sAsk::getAskById($ask_id);
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_id, $desc );
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
		$ask_id     = $this->post('ask_id', 'int');
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
        $ask    = sAsk::getAskById($ask_id);

        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );
        //还是单张图片的求助
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_ids[0], $desc);

        return $this->output([
            'id' => $reply->id,
            'ask_id' => $ask->id
        ]);
    }

    public function upReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        $ret = sReply::updateReplyCount($id, 'up', $status );
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

        $ret    = sReply::updateReplyCount($id, 'inform', $status);
        return $this->output();
    }
}
