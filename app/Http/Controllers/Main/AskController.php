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
        $type = $this->post('type', 'string');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 720);
        $uid  = $this->post('uid', 'int');

        $cond = array();
        $cond['uid'] = $uid;

        $asks = sAsk::getAsksByCond($cond, $page, $size);
        if($type == 'ask') for($i = 0; $i < sizeof($asks); $i++) {
            $asks[$i]['replies'] = sReply::getReplies( array('ask_id'=>$asks[$i]['ask_id']), $page, $size );
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
        $category_id= $this->post( 'category_id', 'int');

        if( !$upload_ids || empty($upload_ids) ) {
            return error('EMPTY_UPLOAD_ID');
        }

        $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc, $category_id );
        //$ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc );
        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );
        //保存标签，由于是发布求助，因此可以直接add
        foreach($tag_ids as $tag_id) {
            sThreadTag::addTagToThread( $this->_uid, mAsk::TYPE_ASK, $ask->id, $tag_id );
        }

        return $this->output([
            'ask_id' => $ask->id
        ]);

    }

    public function save() {
        $id = $this->post('id', 'int');
        $upload_id = $this->post('upload_id', 'int');
        $desc = $this->post('desc', 'string');
        $category_id= $this->post( 'category_id', 'int');

        if($id && $ask = sAsk::getAskById($id)) {
            if($ask->uid != $this->_uid) 
                return error('ASK_NOT_EXIST');
            $ask->desc = $desc;
            $ask->save();
        }
        else if($upload = sUpload::getUploadById($upload_id) ){
            $upload_ids = array($upload_id);
            //$ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc );
            $ask    = sAsk::addNewAsk( $this->_uid, $upload_ids, $desc, $category_id );
            $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );
            //保存标签，由于是发布求助，因此可以直接add
            foreach($tag_ids as $tag_id) {
                sThreadTag::addTagToThread( $this->_uid, mAsk::TYPE_ASK, $ask->id, $tag_id );
            }
        }
        else {
            return error('SYSTEM_ERROR', '保存失败');
        }

        return $this->output([
            'ask_id' => $ask->id
        ]);
    }

    //点赞
    public function upAskAction() {
        $this->isLogin();

        $id     = $this->get('id', 'int');
        $status = $this->get('status', 'int', 1);

        sAsk::upAsk($id, $status);
        return $this->output();
    }
}
?>
