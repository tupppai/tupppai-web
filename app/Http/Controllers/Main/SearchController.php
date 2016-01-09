<?php 
namespace App\Http\Controllers\Main; 

use App\Services\User as sUser;
use App\Services\Download as sDownload;
use App\Services\Ask as sAsk;
use App\Services\Follow as sFollow;
use App\Services\Thread as sThread;
use App\Services\Message as sMessage;
use App\Services\Reply as sReply;
use App\Services\Bbs\Topic as sTopic;

use Session;

class SearchController extends ControllerBase {
    public $_allow = array('*');
    
    public function index() {
        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $type  = $this->get('type', 'string');
        $keyword = $this->get('keyword', 'string');

        $users = array();
        $threads = array();
            $topics = array();

        switch($type) {
        case 'user':
            $users = sUser::searchUserByName($keyword, $page, $size);
            break;
        case 'thread':
            $threads = sThread::searchThreads($keyword, $page, $size);
            break;
        case 'topic':
            $topics = array();
            break;
        default:
            $users = sUser::searchUserByName($keyword, $page, $size);
            $threads = sThread::searchThreads($keyword, $page, $size);
            $topics = array();
            break;
        }
        return $this->output(array(
            'users'=>$users,
            'threads'=>$threads,
            'topics'=>$topics
        ));
    }

    public function users() {
        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $type  = $this->get('type', 'string');
        $keyword = $this->get('keyword', 'string');
        
        $users = sUser::searchUserByName($keyword, $page, $size);

        return $this->output($users);
    }
    public function threads(){ 
        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $type  = $this->get('type', 'string');
        $keyword = $this->get('keyword', 'string');
        
        $threads = sThread::searchThreads($keyword, $page, $size);

        return $this->output($threads);
    }
    public function topics(){
        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $type  = $this->get('type', 'string');
        $keyword = $this->get('keyword', 'string');

        $topics = sTopic::searchTopics($keyword, $page, $size);
        return $this->output($topics);
    }
}
?>
