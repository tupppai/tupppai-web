<?php namespace App\Http\Controllers\Admin;

use App\Models\User as mUser;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Role as mRole;
use App\Models\UserScheduling as mUserScheduling;
use App\Models\UserRole as mUserRole;
use App\Models\ActionLog as mActionLog;

use App\Services\User as sUser, 
    App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\UserScheduling as sUserScheduling,
    App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;

class VerifyController extends ControllerBase
{

    public function threadAction(){

        return $this->output();
	}

    public function list_threadsAction() {
        $this->rowLength  = 4;

        $beg_time = $this->post('beg_time', 'string');        
        $end_time = $this->post('end_time', 'string');

        $type     = $this->post('type', 'int');        
        $role_id  = $this->post('role_id', 'int');

        $user   = new mUser;
        $ask    = new mAsk;
        $reply  = new mReply;
        // 检索条件
        $cond = array();
        $cond[$user->getTable().'.uid']        = $this->post("uid", "int");
        $cond[$user->getTable().'.username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );
        $join = array();
        $join['User'] = 'uid';
        
        $arr = array();

        $asks      = $this->get_threads($ask, $cond, $join);
        $replies   = $this->get_threads($reply, $cond, $join);

        $data   = array_merge($asks['data'], $replies['data']);
        $total  = $asks['recordsTotal'] + $replies['recordsTotal'];

        return $this->output_grid($data, $total);
    }

    private function get_threads($model, $cond, $join){
        $cond[$model->getTable().'.id']      = $this->post('id', 'int');
        $cond[$model->getTable().'.desc']    = $this->post('desc', 'string');
        $orderBy = array($model->getTable().'.create_time desc');
        $data    = $this->page($model, $cond, $join, $orderBy);

        $data['data'] = $this->format($data['data']);
        return $data;
    }

    private function format($data){
        $arr = array();
        foreach($data as $row) {
            $uploads = sUpload::getUploadByIds(explode(',', $row->upload_id));
            foreach($uploads as $upload) {
                $upload->image_url = CloudCDN::file_url($upload->savename);
            }
            $row->uploads = $uploads;

            $arr[$row->create_time] = $row;
        }

        return $arr;
    }
}
