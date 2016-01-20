<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ask;
use App\Models\Reply;
use App\Models\ActionLog;
use App\Models\Usermeta;
use App\Models\Label;
use App\Models\UserScore;
use App\Models\UserRole;

use App\Models\Role as mRole,
    App\Models\Reply as mReply,
    App\Models\User as mUser,
    App\Models\Label as mLabel;

use App\Services\Evaluation as sEvaluation,
    App\Services\UserRole as sUserRole,
    App\Services\Label as sLabel,
    App\Services\Reply as sReply,
    App\Services\UserScore as sUserScore;

use App\Facades\CloudCDN, Form, Html;

class CheckController extends ControllerBase
{

    public function check_sessionAction($session_id) {
        @session_destroy();
        session_id($session_id);
        session_start();
        pr($_SESSION);
    }

    public function previewAction(){
        $id     = $this->get("id", "int");
        $type   = $this->get("type", "int", Label::TYPE_ASK);

        if($type == Label::TYPE_ASK){
            $model = sAsk::getAskById($id);
            $data  = sAsk::detail($model);
        }
        else {
            $model = sReply::getReplyById($id);
            $data  = sReply::detail($model);
        }
        /*
        $data['labels'] = $model->get_labels_array();
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->model = $data;
         */

        return $this->output();
    }

    public function indexAction() {

        return $this->output();
    }

    public function waitAction() {

        return $this->output();
    }

    public function passAction() {

        return $this->output();
    }

    public function rejectAction() {

        return $this->output();
    }

    public function releaseAction() {

        return $this->output();
    }

    public function deleteAction() {

        return $this->output();
    }

    public function list_repliesAction()
    {
        $uid    = $this->post('uid','int');
        $status = $this->get("status", "int", 3);
        $username = $this->post('username', 'string');
        $nickname = $this->post('nickname', 'string');


        $uids = sUserRole::getUidsByIds(mRole::TYPE_PARTTIME);
        $uid_arr = array();
        foreach($uids as $uid){
            $uid_arr[] = $uid;
        }

        if( $uid && in_array($uid, $uid_arr) ){
            $uid_arr = array( $uid );
        }
        $uid_str = implode(",", $uid_arr);
        $reply = new mReply;
        $user  = new mUser;
        // 检索条件
        $cond = array();
        $cond[$reply->getTable().'.status'] = $status;
        // 需求变更，当状态为2的时候，需要把删除的作品也拿出来
        $cond[$reply->getTable().'.uid']  = array(
            $uid_str,
            "IN"
        );
        $cond[$user->getTable().'.username'] = $username;
        $cond[$user->getTable().'.username'] = $nickname;

        // 关联表数据结构
        $join = array();
        $join['User'] = 'uid';
        $join['Upload'] = array('upload_id', 'id');

        $order = array($reply->getTable().'.update_time desc');
        if($status == 3){
            $order = array($reply->getTable().'.id ASC');
        }

        // 用于遍历修改数据
        $data  = $this->page($reply, $cond ,$join, $order);

        // 审批的意见
        $evaluations = sEvaluation::getUserEvaluations($this->_uid);

        foreach($data['data'] as $row){
            $row_id = $row->id;
            $row->content = '';
            $totalScore = sUserScore::getBalance($row->uid);
            $stat = sUserScore::getUserStat( $row->uid );
            $row->stat = Form::label(
                'today',
                '今日：'.$stat['today_passed'].' / '.$stat['today_denied'],
                array(
                    'class'=>'today'
                )
            );
            $row->stat .= Form::label(
                'yesterday',
                '昨日：'.$stat['yesterday_passed'].' / '.$stat['yesterday_denied'],
                array(
                    'class'=>'yesterday'
                )
            );
            $row->stat .= Form::label(
                'last7days',
                '上周：'.$stat['last7days_passed'].' / '.$stat['last7days_denied'],
                array(
                    'class'=>'last7days'
                )
            );
            $row->stat .= Form::label(
                'success',
                '合计：'.$stat['passed'].' / '.$stat['denied'],
                array(
                    'class'=>'success'
                )
            );
            $row->stat .= Form::label(
                'total',
                '总分：'.($totalScore[0] + $totalScore[1]),
                array(
                    'class'=>'total'
                )
            );
            $row->delete    = Form::button('删除作品', array(
                'class'=>'del btn red',
                'type'=>'button',
                'data'=>$row_id
            ));
            $row->recover   = Form::button('重新审核', array(
                'class'=>'recover btn green',
                'type'=>'button',
                'data'=>$row_id
            ));
            $pc_host = env('MAIN_HOST');

            $row->image_url = CloudCDN::file_url($row->savename);

            $row->username .= Form::label('nickname', $row->nickname.'(uid:'.$row->uid.')', array(
                'class'=>'btn-block'
            ));
            $row->username .= Html::image($row->avatar, 'avatar', array(
                'style'=>'border-radius: 50% !important;',
                'width'=>50
            ));
            $row->username .= Html::link(
                "http://$pc_host/index.html#replydetailplay/$row->ask_id/$row->id",
                '查看原图',
                array(
                    'target'=>'_blank',
                    'class'=>'btn-block'
                )
            );
            $row->create_time = Form::label(
                'create_time',
                date("m-d H:i:s", $row->create_time)
            )."<p class='counting'></p>";

            $labels = sLabel::getLabels(mLabel::TYPE_ASK, $row->ask_id, 0, 0);
            $desc = array();
            foreach($labels as $label) {
                $desc[] = $label['content'];
            }
            $row->ask_image = '<div class="wait-image-height">'.
                $this->format_image($row->image_url, array(
                    'type'=>Label::TYPE_ASK,
                    'model_id'=>$row->ask_id
                )).
                '</div>'.'<div class="image-url-content">'.implode(",", $desc).'</div>';

            $labels = sLabel::getLabels(mLabel::TYPE_REPLY, $row->id, 0, 0);
            $desc = array();
            foreach($labels as $label) {
                $desc[] = $label['content'];
            }
            $row->thumb_url = '<div class="wait-image-height">'.
                $this->format_image($row->image_url, array(
                    'type'=>Label::TYPE_REPLY,
                    'model_id'=>$row->id
                )).
                '</div>'.'<div class="image-url-content">'.implode(",", $desc).'</div>';
            $row->auditor = '无(未实现)';

            /*
            $audit = UserScore::oper_user(Label::TYPE_REPLY, $row->id )->toArray();
            if($audit){
                $audit = $audit[0];
                if( $audit['uid'] ){
                    $row->auditor = $audit['username']."<p>昵称:".$audit['nickname']."</p>".
                "<p><img width='50' style='border-radius: 50% !important;
              height: 50px;
              width: 50px;' src='".$audit['avatar']."' /></p>";
                }
            }*/

            switch($cond[$reply->getTable().'.status']){
            case Reply::STATUS_READY:
            default:
                $e_str = "";
                $o_str = "";
                foreach($evaluations as $e){
                    $e_str .= '<li><button data="'.$row_id.'" class="form-control quick-deny">'.$e->content.'</button></li>';
                    $o_str .= '<option class="quick-deny" value="'.$e->id.'">'.$e->id.'.'.$e->content.'</option>';
                }
                $row->oper = '
                    <div>
                        通过：<div>
                            <button class="btn green button-pass" type="button" data-toggle="dropdown">通过</button>
                            <ul class="dropdown-menu" role="menu">
                            <li><button data="'.$row_id.'" class="score form-control">1 分</button></li>
                            <li><button data="'.$row_id.'" class="score form-control">2 分</button></li>
                            <li><button data="'.$row_id.'" class="score form-control">3 分</button></li>
                            <li><button data="'.$row_id.'" class="score form-control">4 分</button></li>
                            <li><button data="'.$row_id.'" class="score form-control">5 分</button></li>
                            </ul>
                        </div>
                    </div>
                    <div>
                    拒绝：<a class="deny" data-toggle="modal" href="#modal_evaluation" data="'.$row_id.'">管理</a><div >
                        <select name="reason" class="flexselect">
                            '.$o_str .'
                        </select>
                        <ul class="dropdown-menu deny-reasons" role="menu"><div class="li_container">
                        '.$e_str .'
                        </div><li><a class="btn deny" data-toggle="modal" href="#modal_evaluation" data="'.$row_id.'">管理</a></li>
                        </ul>
                        <button class="btn red button-deny reject_btn" type="button" data-toggle="dropdown">拒绝</button>
                    </div></div>';
                    //<a class="deny btn red" data-toggle="modal" href="#modal_evaluation" data="'.$row_id.'">deny</a>';
                    //<button class="deny btn red btn-xs" type="button" data="'.$row_id.'">deny</button>';
                break;
            case Reply::STATUS_NORMAL:
                //$user_score = UserScore::findFirst("type=".UserScore::TYPE_REPLY." and item_id=".$row->id);
                $row->score = '(未实现)';
                //if($user_score) {
                    //$row->score = $user_score->score;
                //}
                break;
            case Reply::STATUS_DELETED:
            case Reply::STATUS_REJECT:
                //$user_score = UserScore::findFirst("type=".UserScore::TYPE_REPLY." and item_id=".$row->id);
                //if($user_score)
                    //$row->content = $user_score->content;
                $row->content = '(未实现)';
                break;
            }
        }
        // 输出json
        return $this->output_table($data);
    }


    public function set_statusAction(){
        $reply_id  = $this->post("reply_id", "int");
        $status    = $this->post("status", "int");
        $data      = $this->post("data", "string", 0);

        if(!$reply_id or !isset($status)){
            return error('WRONG_ARGUMENTS');
        }

        $reply = sReply::getReplyById($reply_id);
        sReply::updateReplyStatus($reply, $status, $this->_uid, $data);

        return $this->output();
    }

    public function get_evaluationsAction(){
        $uid = $this->_uid;

        $evaluations = sEvaluation::getUserEvaluations($uid);
        return $this->output($evaluations);
    }

    public function set_evaluationAction(){
        $data   = $this->post("data", "string");
        $uid    = $this->_uid;

        $evaluation = sEvaluation::setEvaluation($uid, $data);
        return $this->output($evaluation);
    }
}

