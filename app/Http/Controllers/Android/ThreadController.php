<?php
namespace App\Http\Controllers\Android;

use App\Services\User as sUser;

class ThreadController extends ControllerBase{

    public function subscribedAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $items = sUser::getSubscribed($this->_uid, $page, $width, $last_updated);
        
        return $this->output( $items );
    }







    public function fellowsDynamicAction() {
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $width= $this->get("width", "int", 480);
        $last_updated = $this->get('last_updated', 'int', time());
        $uid  = $this->_uid;

        $data = array();
        $items = User::getFellowsDynamicID($uid, $page, $size);

        $counter = 0;
        foreach ($items as $item) {
            if($counter ++ == 0)
                continue;
            switch ($item['type']) {
                case Label::TYPE_ASK:
                    $ask = Ask::findFirst($item['id']);
                    if($ask) {
                        $data[] = $ask->toStandardArray($uid, $width);
                    }
                    break;
                case Label::TYPE_REPLY:
                    $reply = Reply::findFirst($item['id']);
                    if($reply) {
                        $data[] = $reply->toStandardArray($uid, $width);
                    }
                    break;
                default:
                    break;
            }
        }

        return $this->output( $data );
    }
}
