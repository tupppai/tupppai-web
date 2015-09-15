<?php 
namespace App\Http\Controllers\Main;

use App\Services\Ask As sAsk,
    App\Services\Reply As sReply;

class AskController extends ControllerBase {
    
    public $_allow = array('getAsksByType', 'show','test', 'detail');    

    /**
     * 获取首页数据
     * @author brandwang
     */
    public function getAsksByTypeAction() {
        $type = $this->post('type', 'string', 'new');
        $page = $this->post('page', 'int',1);
        $size = $this->post('size', 'int',15);
        $width= $this->post('width', 'int', 300);

        $cond = array();

        $asks = sAsk::getAsksByType($cond, $type, $page, $size);
        //$sum  = sAsk::sumAsksByType($type, $cond);
        
        return $this->output($asks);
    }

    /**
     * ask show page
     *
     * @params ask id 
     * @author brandwang
     */
    public function showAction($id) {
        if (!isset($id)) {
            return error('EMPTY_ID');
            $this->back();
        }

        // get reply items by ask id
        $page   = $this->get('page', 'int', 1);
        $width  = $this->get('width', 'int', 560);
        $size   = $this->get('size', 'int', 10);
        
        $cond   = array();
        $reply_items = sReply::getRepliesByAskId($id, $page, $size);
         
        // get origin ask item
        $ask        = sAsk::getAskById($id);
        $ask_item   = sAsk::detail($ask);
        
        return $this->output(array(
            'reply_items' => $reply_items,
            'ask_item' => $ask_item
        )); 
    }
    
    /**
     *  临时页面
     */
    public function detailAction($id) {
        if (!isset($id)) {
            return error('EMPTY_ID');
            //$this->back();
        }

        // get reply items by ask id
        $page   = $this->get('page', 'int', 1);
        $width  = $this->get('width', 'int', 560);
        $size   = $this->get('size', 'int', 10);
        
        // get origin ask item
        $ask        = sAsk::getAskById($id);
        $ask_item   = sAsk::detail($ask);
        
        return $this->output(array(
            'ask_item' => $ask_item
        )); 
    }

    //点赞
    public function upAskAction() {
        $id     = $this->get('id', 'int');
        $status = $this->get('status', 'int', 1);

        $ret    = sAsk::updateAskCount($id, 'up', $status);
        return $this->output();
    }
}
?>
