<?php 
namespace App\Http\Controllers\Main;

use App\Services\Ask as sAsk,
    App\Services\Comment as sComment,
    App\Services\Upload as sUpload,
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

        $asks = sAsk::getAsksByType($cond, $type, $page, $size, $this->_uid );
        for($i = 0; $i < sizeof($asks); $i++) {
            $asks[$i]['replyers'] = sAsk::getReplyers($asks[$i]['id'], 0, 6);
        }
        
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
