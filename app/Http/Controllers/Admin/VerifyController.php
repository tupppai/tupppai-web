<?php namespace App\Http\Controllers\Admin;

use App\Models\User as mUser;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Role as mRole;
use App\Models\UserScheduling as mUserScheduling;
use App\Models\UserRole as mUserRole;
use App\Models\ActionLog as mActionLog;
use App\Models\Category as mCategory;

use App\Services\User as sUser, 
    App\Services\Role as sRole,
    App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\Category as sCategory,
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

        $ask_arr   = $asks['data'];
        $reply_arr = $replies['data'];
        sort($ask_arr);
        sort($reply_arr);

        $data   = array_merge($ask_arr, $reply_arr);
        sort($data);
        $data   = array_slice($data, 0, sizeof($data)/2);

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

    private function format($data, $index = null){
        $arr = array();
        $categories = sCategory::getCategories();
        $roles      = array_reverse(sRole::getRoles()->toArray());

        foreach($data as $row) {
            $index = $row->create_time;
            $uploads = sUpload::getUploadByIds(explode(',', $row->upload_id));
            foreach($uploads as $upload) {
                $upload->image_url = CloudCDN::file_url($upload->savename);
            }

            $row->type    = $row->getTable();
            $desc = json_decode($row->desc);
            $row->desc    = is_array($desc)? $desc[0]->content: $row->desc;
            $row->uploads = $uploads;
            $row->roles   = $roles;
            $role_id      = sUserRole::getFirstRoleIdByUid($row->uid);
            $row->role_id     = $role_id;
            $row->categories  = $categories;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);

            $arr[$index] = $row;
        }

        return $arr;
    }
}
