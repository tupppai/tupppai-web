<?php 
namespace App\Http\Controllers\Main;

use App\Services\Ask as sAsk,
    App\Services\Comment as sComment,
    App\Services\Upload as sUpload,
    App\Services\User as sUser,
    App\Services\Reply as sReply;

use App\Models\Comment as mComment;

class AskController extends ControllerBase {
    
    public $_allow = array('*');    

    public function index(){
        $type = $this->post('type', 'string', 'new');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 300);
        $uid  = $this->post('uid', 'int');

        $cond = array();
        $cond['uid'] = $uid;

        $asks = sAsk::getAsksByCond($cond, $page, $size);

        return $this->output($asks);
    }


    public function view($id) {
        $ask = sAsk::getAskById($id);
        $ask = sAsk::detail($ask);

        return $this->output($ask);
    }

    public function multi(){
        $upload_ids = $this->post( 'upload_ids', 'json_array' );
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

        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc );
        $user   = sUser::addUserAskCount( $this->_uid );
        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );

        return $this->output([
            'ask_id' => $ask->id
        ]);

    }

    public function save() {
        $upload_id = $this->post('upload_id', 'int');
        $desc = $this->post('desc', 'string');

        $upload_ids = array($upload_id);
        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc );
        $user   = sUser::addUserAskCount( $this->_uid );

        return $this->output([
            'ask_id' => $ask->id
        ]);
    }

    //点赞
    public function upAskAction() {
        $this->isLogin();

        $id     = $this->get('id', 'int');
        $status = $this->get('status', 'int', 1);

        $ret    = sAsk::updateAskCount($id, 'up', $status);
        return $this->output();
    }
}
?>
