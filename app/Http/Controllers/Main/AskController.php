<?php 
namespace App\Http\Controllers\Main;

use App\Services\Ask As AskService;

class AskController extends ControllerBase {
    
    /**
     * 获取首页数据
     * @author brandwang
     */
    public function getAsksByTypeAction() {
        $type = $this->post('type', 'hot');
        $page = $this->post('page', 'int',1);
        $page_size = 15;
        
        $ask_items = AskService::getAsksByType($type, $page, $page_size);
        return json_encode($ask_items);
    }
}
?>
