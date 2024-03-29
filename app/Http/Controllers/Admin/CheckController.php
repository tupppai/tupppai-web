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
use App\Trades\User as tUser;

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
            $uids = array_column( $uids->toArray(), 'uid' );

            $cond[$tAsgnmnt.'.assigned_to']  = [ $uids, "IN" ];
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
                $cond[$tAsgnmnt.'.grade']  = [0, '>'];
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
                    <button class="btn btn-success" type="button" data-toggle="collapse" data-target="#acceptWork_'.$row->id.'" aria-expanded="false" aria-controls="acceptWork_'.$row->id.'">
                      通过
                    </button>
                    <div class="collapse pass" data-aid="'.$row->id.'" id="acceptWork_'.$row->id.'">
                      <div class="well">
                            <select name="reward_uid" id="reward_uid_'.$row->id.'"></select>
                            <input type="text" class="reward_amount" placeholder="奖励金额" />
                            <button class="btn green reward_work" type="button">通过</button>
                      </div>
                    </div>


                    <button class="btn btn-deny red" type="button" data-toggle="collapse" data-target="#rejectWork_'.$row->id.'" aria-expanded="false" aria-controls="rejectWork_'.$row->id.'">
                      拒绝
                    </button>
                    <div class="collapse deny" data-aid="'.$row->id.'" id="rejectWork_'.$row->id.'">
                      <div class="well">
                        <select name="reason" class="flexselect">'.$o_str .'</select>
                        <ul class="dropdown-menu deny-reasons" role="menu">
                            <div class="li_container">'.$e_str .'</div>
                        </ul>
                        <button class="btn red button-deny reject_btn" type="button" data-aid='.$row->id.'>拒绝</button>
                        <a class="deny" data-toggle="modal" href="#modal_evaluation" data="'.$row->reply_id.'">管理</a>
                      </div>
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
        $aid    = $this->post( 'aid', 'int' );
        $grade  = $this->post( 'score', 'int' );
        $amount = $this->post( 'amount', 'money' );
        $from_uid = $this->post('from_uid', 'int');
        $reason = $this->post( 'reason', 'string' );

        $asgnmnt = sAssignment::verifyTask( $this->_uid, $aid, $grade, $reason );
        if( $amount ){
            if( !sUser::checkUserExistByUid($from_uid) ){
                return error('USER_NOT_EXIST', '来源用户不存在');
            }
            if( !sUser::checkUserExistByUid($asgnmnt->assigned_to) ){
                return error('USER_NOT_EXIST', '目标用户不存在');
            }
            tUser::pay( $from_uid, $asgnmnt->assigned_to, $amount, '模拟用户打赏' );
        }

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

