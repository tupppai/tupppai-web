<?php namespace App\Http\Controllers\Android;

use App\Models\Label as mLabel;

use App\Services\Count as sCount,
    App\Services\Reply as sReply,
    App\Services\Collection as sCollection,
    App\Services\Ask as sAsk,
    App\Services\User as sUser;

class ReplyController extends ControllerBase
{
    /**
     * 回复作品
     */
	public function saveAction()
    {
		$ask_id     = $this->post('ask_id', 'int');
        $upload_id  = $this->post('upload_id', 'int');
        $label_str  = $this->post('labels');
        $uid        = $this->_uid;

        $reply  = sReply::addNewReply( $uid, $label_str, $ask_id, $upload_id );
        $user   = sUser::addUserReplyCount($uid);

        $labels = json_decode($labels_str, true);
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
            'reply_id'=> $reply->id,
            'labels'=>$ret_labels
        ));
	}

    public function upReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        $ret    = sReply::updateReplyCount($id, 'up', $status);
        return $this->output();
    }

    public function collectReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        $ret    = sCollection::collectReply($uid, $id, $status);
        return $this->output();
    }

    public function informReplyAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        $ret    = sReply::updateReplyCount($id, 'inform', $status);
        return $this->output();
    }
}
