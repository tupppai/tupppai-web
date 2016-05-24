<?php namespace App\Http\Controllers\Admin;

use App\Models\Ask;
use App\Models\ActionLog;
use App\Models\Usermeta;
use App\Models\UserScore;
use App\Models\UserRole;

use App\Models\Role as mRole,
    App\Models\Reply as mReply,
    App\Models\User as mUser,
    App\Models\Label as mLabel;
use App\Models\Parttime\Assignment as mAssignment;

use App\Services\Evaluation as sEvaluation,
    App\Services\UserRole as sUserRole,
    App\Services\Label as sLabel,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\UserScore as sUserScore;
use App\Services\User as sUser;
use App\Services\Parttime\Assignment as sAssignment;

use App\Facades\CloudCDN, Form, Html;

class CheckController extends ControllerBase
{
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

    public function list_worksAction(){
        $uid    = $this->post('uid','int');
        $page    = $this->post('page','int', 1 );
        $size    = $this->post('size','int', 15 );
        $type     = $this->post('type','string');
        $nickname = $this->post('nickname', 'string');

        $cond = [];

        $asgnmnt = new mAssignment;
        $ptdb = 'psgod_parttime';
        $tAsgnmnt = $ptdb.'.'.$asgnmnt->getTable();
        if( $nickname ){
            $uids = sUser::getFuzzyUsersByIdAndName( $nickname );

            $cond[$tAsgnmnt.'.assigned_to']  = array(
                $uids,
                "IN"
            );
        }
        else{
            $cond[$tAsgnmnt.'.assigned_to'] = $uid;
        }

        switch($type){
            case 'done':
                $cond[$tAsgnmnt.'.status'] = mAssignment::ASSIGNMENT_STATUS_FINISHED;
                break;
            case 'checked':
                $cond[$tAsgnmnt.'.status'] = mAssignment::ASSIGNMENT_STATUS_GRADED;
                $cond[$tAsgnmnt.'.grade']  = [0, '!='];
                break;
            case 'rejected':
                $cond[$tAsgnmnt.'.status'] = mAssignment::ASSIGNMENT_STATUS_GRADED;
                $cond[$tAsgnmnt.'.grade']  = 0;
                break;
            case 'refused':
                $cond[$tAsgnmnt.'.status'] = mAssignment::ASSIGNMENT_STATUS_REFUSE;
                break;
            default:
                return false;
        }

        // 用于遍历修改数据
        $data  = $this->page($asgnmnt, $cond);
        // 审批的意见
        $evaluations = sEvaluation::getUserEvaluations($this->_uid);

        foreach($data['data'] as $row){
            $user = sUser::getUserByUid( $row->assigned_to );
            $pc_host = env('MAIN_HOST');
            $ask = sAsk::getAskById( $row->ask_id );

            if( $ask ){
                $ask = sAsk::detail( $ask );
            }
            if( $row->reply_id ){
                $reply = sReply::getReplyById( $row->reply_id );
                if( !$reply ){
                    continue;
                }
                $reply = sReply::detail( $reply );
            }

            $user = sUser::getUserByUid( $row->assigned_to );


            $row->author = Form::label('nickname', $user->nickname.'(uid:'.$user->uid.')', array(
                'class'=>'btn-block'
            ));
            $row->author .= Html::image($user->avatar, 'avatar', array(
                'style'=>'border-radius: 50% !important;',
                'width'=>50
            ));
            $row->author .= Html::link(
                "http://$pc_host/index.html#replydetailplay/$row->ask_id/$row->reply_id",
                '查看原图',
                array(
                    'target'=>'_blank',
                    'class'=>'btn-block'
                )
            );

            $row->auditor = '(无)';
            if( $row->oper_by ){
                $auditor = sUser::getUserByUid( $row->oper_by );
                $row->auditor = $auditor->nickname.'(uid:'.$auditor->uid.')';
            }

            $row->create_time = Form::label(
                'create_time',
                date("m-d H:i:s", $row->create_time)
            )."<p class='counting'></p>";

            $row->update_time = Form::label(
                'update_time',
                date("m-d H:i:s", $row->update_time)
            )."<p class='counting'></p>";

            $row->ask = '<div class="wait-image-height">'.
                $this->format_image($ask['image_url'], array(
                    'type'=>mLabel::TYPE_ASK,
                    'model_id'=>$row->ask_id
                )).'</div>'.'<div class="image-url-content">'.$ask['desc'].'</div>';

            $row->reply = '';
            $row->reply_upload_time = '';
            if( $row->reply_id ){
                $row->reply = '<div class="wait-image-height">'.
                    $this->format_image($reply['image_url'], array(
                        'type'=>mLabel::TYPE_REPLY,
                        'model_id'=>$row->reply_id
                    )).
                    '</div>'.'<div class="image-url-content">'.$reply['desc'].'</div>';
                $row->reply_upload_time = date("m-d H:i:s", $reply['update_time']);
            }

            $o_str = ''; //曾用理由列表
            $e_str = ''; //？
            switch($row->status){
                case mAssignment::ASSIGNMENT_STATUS_FINISHED:
                    $o_str = "";
                    foreach($evaluations as $e){
                        $e_str .= '<li><button data="'.$row->id.'" class="form-control quick-deny">'.$e->content.'</button></li>';
                        $o_str .= '<option class="quick-deny" value="'.$e->id.'">'.$e->id.'.'.$e->content.'</option>';
                    }

                    $row->oper = '

                    <div class="btn-group">
                      <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        通过
                      </button>
                      <ul class="dropdown-menu">
                        <li><button data-aid="'.$row->id.'" data-score="1" class="score form-control">1 分</button></li>
                        <li><button data-aid="'.$row->id.'" data-score="2" class="score form-control">2 分</button></li>
                        <li><button data-aid="'.$row->id.'" data-score="3" class="score form-control">3 分</button></li>
                        <li><button data-aid="'.$row->id.'" data-score="4" class="score form-control">4 分</button></li>
                        <li><button data-aid="'.$row->id.'" data-score="5" class="score form-control">5 分</button></li>
                      </ul>
                    </div>


                    <div class="btn-group">
                      <button type="button" class="btn red btn-deny dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">拒绝</button>
                      <ul class="dropdown-menu">
                        <li>
                        <div class="popover-content">
                            <a class="deny" data-toggle="modal" href="#modal_evaluation" data="'.$row->reply_id.'">管理</a>
                            <select name="reason" class="flexselect">'.$o_str .'</select>
                            <ul class="dropdown-menu deny-reasons" role="menu">
                                <div class="li_container">'.$e_str .'</div>
                                <li><a class="btn deny" data-toggle="modal" href="#modal_evaluation" data="'.$row->reply_id.'">管理</a></li>
                            </ul>
                        </div>
                        <button class="btn red button-deny reject_btn" type="button" data-toggle="dropdown">拒绝</button>
                        </li>
                      </ul>
                    </div>';
                    break;
                case mAssignment::ASSIGNMENT_STATUS_GRADED:
                    if( $row->grade ){
                        //tongguo
                    }
                    else{
                        //jujue
                    }
                    break;
                case mAssignment::ASSIGNMENT_STATUS_REFUSE:
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

    public function verify_taskAction(){
        $aid = $this->post( 'aid', 'int' );
        $grade = $this->post( 'score', 'int' );
        $reason = $this->post( 'reason', 'string' );

        $asgnmnt = sAssignment::verifyTask( $aid, $grade, $reason );

        return $this->output($asgnmnt);
    }

    //获取拒绝理由
    public function get_evaluationsAction(){
        $uid = $this->_uid;

        $evaluations = sEvaluation::getUserEvaluations($uid);
        return $this->output($evaluations);
    }

    //新增拒绝理由
    public function set_evaluationAction(){
        $data   = $this->post("data", "string");
        $uid    = $this->_uid;

        $evaluation = sEvaluation::setEvaluation($uid, $data);
        return $this->output($evaluation);
    }
}

