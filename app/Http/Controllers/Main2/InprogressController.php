<?php 
namespace App\Http\Controllers\Main2;

use App\Services\Download as sDownload;

class InprogressController extends ControllerBase {

    public $_allow = '*';
    public function index(){
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 721);
        $uid  = $this->post('uid', 'int', $this->_uid);

        $inprogresses = sDownload::getDownloaded($uid, $page, $size, time());
        
        return $this->output($inprogresses);
    }

    public function view($id) {
        $ask = sAsk::getAskById($id);
        $ask = sAsk::info($ask);

        return $this->output($ask);
    }

    public function del() {
        $id  = $this->get('id', 'int');
        $uid = $this->_uid;

        $download = sDownload::getDownloadById($id);
        sDownload::deleteDLRecord($uid, $download->target_id);

        return $this->output();
    }
}
?>
