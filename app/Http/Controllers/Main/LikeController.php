<?php namespace App\Http\Controllers\Main;

use App\Models\App;
use App\Models\ActionLog;

use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Count as mCount;

use App\Services\User as sUser;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\Count as sCount;
use App\Services\Comment as sComment;

class LikeController extends ControllerBase {

    // page index
    public function save() {
        $this->isLogin();

        $id     = $this->get('id', 'int');
        if(!$id) {
            return error('EMPTY_ID');
        }

        //默认点赞作品
        $type   = $this->get('type', 'int', mCount::TYPE_REPLY);
        $status = $this->get('status', 'int', mCount::STATUS_NORMAL);

        switch($type) {
        case mCount::TYPE_ASK:
            sAsk::upAsk($id, $status);
            break;
        case mCount::TYPE_REPLY:
            sReply::upReply($id, $status);
            break;
        case mCount::TYPE_COMMENT:
            sComment::updateCommentCount( $id, 'up', $status );
            break;
        }

        return $this->output();
    }

    public function love() {
        $this->isLogin();
        $id     = $this->get('id', 'int');
        $num    = $this->get('num', 'int', 1);
        $status = $this->get('status', 'int', mCount::STATUS_NORMAL);
        $uid    = $this->_uid;

        fire('FRONTEND_HANDLE_LOVE',[$id, $num, $status]);

        return $this->output();
    }
}
