<?php namespace App\Http\Controllers\Android;

use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\User as sUser,
    App\Services\Count as sCount,
    App\Services\Focus as sFocus,
    App\Services\Label as sLabel,
    App\Services\ActionLog as sActionLog,
    App\Services\Invitation as sInvitation;

class AskController extends ControllerBase
{
    /**
     * 首页数据
     */
	public function indexAction()
    {
        //todo: type后续改成数字
        $type   = $this->get('type', 'string', 'hot');

        $width  = $this->get('width', 'int', 480);
		$sort   = $this->get('sort', 'string', 'time');
		$order  = $this->get('order', 'string', 'desc');

		$page   = $this->get('page', 'int', 1);
        $size   = $this->get('size', 'int', 15);

        $asks = sAsk::getAsksByType($type, $page, $size);
        return $this->output($asks);
    }

    /**
     * 求p详情
     */
    public function showAction($ask_id)
    {
    	$page  = $this->get('page', 'int', 1);
		$size  = $this->get('size', 'int', 15);
        $width = $this->get('width', 'int', 480);
        $fold  = $this->get('fold', 'int', 0);

        $ask    = sAsk::getAskById($ask_id);
        $replies= sReply::getRepliesByAskId($ask_id, $page, $size);

        $asks   = array();
        if($page == 1 && $fold == 1){
            $asks[] = $ask;
        }

        $data = array_merge($asks, $replies);

        return $this->output(array(
            'replies'=>$data
        ));
    }

    /**
     * 保存求p
     */
	public function saveAction()
    {
        $upload_id  = $this->post('upload_id', 'int', 3729);
        $label_str  = $this->post('labels');
        $labels = json_decode($label_str, true);

        $ask    = sAsk::addNewAsk($this->_uid, $upload_id, $label_str );
        $user   = sUser::addUserAskCount($this->_uid);

        $ret_labels = array();
        if (is_array($labels)){
            foreach ($labels as $label) {
                $lbl = sLabel::addNewLabel(
                    $label['content'],
                    $label['x'],
                    $label['y'],
                    $this->_uid,
                    $label['direction'],
                    $upload_id,
                    $ask->id
                );
                $ret_labels[$label['vid']] = array('id'=>$lbl->id);
            }
        } 

        return $this->output(array(
            'ask_id'=> $ask->id,
            'labels'=>$ret_labels
        ));
	}

    public function upAskAction($id) {
        $status = $this->get('status', 'int', 1);

        $ret    = sAsk::updateAskCount($id, 'up', $status);
        return $this->output();
    }

    public function informAskAction($id) {
        $status = $this->get('status', 'int', 1);

        $ret    = sAsk::updateAskCount($id, 'inform', $status);
        return $this->output();
    }

    public function focusAskAction($id) {
        $status = $this->get('status', 'int', 1);
        $uid    = $this->_uid;

        $ret    = sFocus::focusAsk($uid, $id, $status);
        return $this->output();
    }
}
