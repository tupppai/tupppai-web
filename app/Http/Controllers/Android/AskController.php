<?php namespace App\Http\Controllers\Android;

use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\User as sUser,
    App\Services\Count as sCount,
    App\Services\Focus as sFocus,
    App\Services\Label as sLabel,
    App\Services\Upload as sUpload,
    App\Services\ActionLog as sActionLog,
    App\Services\Invitation as sInvitation;

use Log;

class AskController extends ControllerBase
{
    /**
     * 首页数据
     */
	public function indexAction()
    {
        //todo: type后续改成数字
        $type   = $this->get('type', 'string', 'hot');
		$page   = $this->get('page', 'int', 1);
        $size   = $this->get('size', 'int', 15);

        $cond   = array();
        $asks = sAsk::getAsksByType($cond, $type, $page, $size);

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

        $ask    = sAsk::detail( sAsk::getAskById($ask_id) );
        $asker  = sUser::getUserByUid( $ask['uid'] );
        $replies= sReply::getRepliesByAskId($ask_id, $page, $size);

        $data = array();
        if($page == 1 && $fold == 1){
            $ask['sex'] = $asker['sex'];
            $ask['nickname'] = $asker['nickname'];
            $ask['avatar'] = $asker['avatar'];
            $data['ask'] = $ask;
        }
        $data['replies'] = $replies;

        return $this->output( $data );
    }

    /**
     * 保存求p
     */
	public function saveAction()
    {
        $upload_ids = $this->post('upload_id', 'string');
        $ratios     = $this->post('ratio', 'float', 0);
        $scales     = $this->post('scale', 'float', 0);

        $desc       = $this->post('desc', 'string', '');
        if( !$upload_id ) {
            return error('EMPTY_UPLOAD_ID');
        }
        $upload = sUpload::updateImages($upload_ids, $scales, $ratios);

        $ask    = sAsk::addNewAsk($this->_uid, $upload_ids, $desc );
        $user   = sUser::addUserAskCount($this->_uid);

        $label_str  = $this->post('labels', 'json');

        $labels     = json_decode($label_str, true);
        $ret_labels = array();
        if (is_array($labels)){
            foreach ($labels as $label) {
                $lbl = sLabel::addNewLabel(
                    $label['content'],
                    $label['x'],
                    $label['y'],
                    $this->_uid,
                    $label['direction'],
                    $upload_ids,
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
        return $this->output( $ret);
    }
}
