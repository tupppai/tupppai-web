<?php 
namespace App\Http\Controllers\Main;

use App\Services\Download as sDownload;

class InprogressController extends ControllerBase {
    
    public function index(){
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 300);

        $inprogresses = sDownload::getDownloaded($this->_uid, $page, $size, time());
        
        return $this->output($inprogresses);
    }

    public function view($id) {
        $ask = sAsk::getAskById($id);
        $ask = sAsk::info($ask);

        return $this->output($ask);
    }
}
?>
