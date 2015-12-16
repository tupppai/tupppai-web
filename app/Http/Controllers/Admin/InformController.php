<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Usermeta;
use App\Models\Comment;
use App\Models\Ask;
use App\Models\Reply;
use App\Models\ActionLog;

use App\Models\User as mUser;
use App\Models\Inform as mInform;

use App\Services\User as sUser,
	App\Services\Reply as sReply,
	App\Services\Inform as sInform,
    App\Services\Comment as sComment;

use Request;

class InformController extends ControllerBase
{

	public function indexAction()
	{

        return $this->output();
	}

	public function list_reportsAction(){
		if( !Request::ajax() ){
			return error('WRONG_ARGUMENTS');
		}

        $inform = new mInform;
        $user   = new mUser;
		$cond = array();
		$order = array();
		$join = array('User'=>'uid');
		$group = array();


		$type = $this->get('type', 'string', 'pending');
		if( $type == 'pending' ){
			$cond[$inform->getTable().'.status'] = mInform::INFORM_STATUS_PENDING;
		}
		else if( $type == 'resolved' ){
			$cond[$inform->getTable().'.status'] = mInform::INFORM_STATUS_SOLVED;
		}
		else {// if( $type == 'all' ){
			//
		}

		$cond[$inform->getTable().'.uid']		= $this->post("uid", "int");
		$cond[$user->getTable().'.username']   = array(
			 $this->post("username", "string"),
			 "LIKE",
			 "AND"
		);
		$cond[$user->getTable().'.nickname']   = array(
			 $this->post("nickname", "string"),
			 "LIKE",
			 "AND"
         );
        $pc_host = env('MAIN_HOST');

		$data  = $this->page($inform, $cond, $join, $order, $group);
        foreach($data['data'] as $row){
            $reporter = sUser::getUserByUid($row->uid);
			$genderColor = get_sex_name($reporter->sex) == '男' ?'lightsteelblue':'pink';
			$avatar = $reporter->avatar ? '<img class="user-portrait" src="'.$reporter->avatar.'" alt="'.$reporter->username.'" style="border: 3px solid '.$genderColor.'"/>':'无头像';
			$row->content = '<a target="_blank" href="http://'.$pc_host.'/user/profile/'.$row->uid.'">'.$avatar.'</a>'.$reporter->username.' '.date('Y-m-d H:i', $row->create_time).'<br />'.$row->content;
			switch( $row->target_type ){
				case mInform::TYPE_ASK:
					$row->object = '<a target="_blank" href="http://'.$pc_host.'/index.html#askdetail/ask/'.$row->target_id.'">查看被举报求助</a>';
					break;
				case mInform::TYPE_REPLY:
					$reply = sReply::getReplyById( $row->target_id );
					$row->object = '<a target="_blank" href="http://'.$pc_host.'/index.html#replydetailplay/'.$reply->ask_id.'/'.$row->target_id.'">查看被举报作品</a>';
					break;
                case mInform::TYPE_COMMENT:
                    $comment = sComment::getCommentById($row->target_id);
					switch( $comment->target_type ){
						case Comment::TYPE_ASK:
							$type = 'ask';
							$row->object = '<a target="_blank" href="http://'.$pc_host.'/#askdetail/ask/'.$comment->target_id.'">查看被举报评论所在对象</a>';
							break;
						case Comment::TYPE_REPLY:
							$type = 'reply';
							$row->object = '<a target="_blank" href="http://'.$pc_host.'/#replydetailplay/0/'.$comment->target_id.'">查看被举报评论所在对象</a>';
							break;
						case Comment::TYPE_COMMENT:
							if( $comment->target_type == Comment::TYPE_ASK ){
								$row->object = '<a target="_blank" href="http://'.$pc_host.'/#askdetail/ask/'.$comment->target_id.'">查看被举报评论所在对象</a>';
							}
							else{
								$row->object = '<a target="_blank" href="http://'.$pc_host.'/#replydetailplay/0/'.$comment->target_id.'">查看被举报评论所在对象</a>';
							}
							break;
						default:
							$type = '';
							break;
					}
					if( $comment->status == Comment::STATUS_DELETED ){
						$row->object = '已被删除';
					}
					break;
				case mInform::TYPE_USER:
					$row->object = '<a target="_blank" href="http://'.$pc_host.'/#homepage/reply/'.$row->target_id.'">查看被举报用户</a>';
					break;
				default:
					break;
			}
			if( $row->status == mInform::INFORM_STATUS_PENDING ){
				$oper = array(
					'<a href="#" data-type="block_reporter" data-id="'.$row->id.'" class="deal_inform">禁言举报者</a>',
					'<a href="#" data-type="block_author" data-id="'.$row->id.'" class="deal_inform">禁言被举报对象作者</a>',
					'<a href="#" data-type="ignore" data-id="'.$row->id.'" class="deal_inform">忽略</a>',
					'<a href="#" data-type="false_report" data-id="'.$row->id.'" class="deal_inform">误报/慌报</a>',
				);
			}
			else{
                $oper = array();
                if($row->oper_by){
				    $processor = sUser::getUserByUid($row->oper_by);
				    if( $processor ){
				    	$oper = array('<a target="_blank" href="http://'.$pc_host.'/user/profile/'.$row->oper_by.'">'.$processor->username.'</a> 在 '.date('Y-m-d H:i:s', $row->oper_time), $row->oper_result);
				    }
				    else{
				    	$oper = array('什么？处理者用户信息不存在？赶紧找只程序猿报Bug！');
                    }
                }
			}
			$row->oper = implode('<br />', $oper);

			switch ($row->status) {
				case mInform::INFORM_STATUS_IGNORED:
					$statusName = '已忽略';
					$color = 'lightgray';
					break;
				case mInform::INFORM_STATUS_PENDING:
					$statusName = '待处理';
					$color = 'dodgerblue';
					break;
				case mInform::INFORM_STATUS_SOLVED:
					$statusName = '已处理';
					$color = 'lightgreen';
					break;
				case mInform::INFORM_STATUS_REPLACED:
					$statusName = '重复举报';
					$color = 'plum';
					break;

				default:
					$statusName = '状态名称不存在？？';
					$color = 'orangered';
					break;
			}
			$row->status = '<span style="color:'.$color.'">'.$statusName.'</span>';
		}


		return $this->output_table($data);
	}

	public function dealAction(){
		$uid = $this->_uid;
		$report_id = $this->post( 'id', 'int' );
		$type = $this->post( 'type', 'string' );

		if( !$report_id ){
			return error('EMPTY_INFORM_ID');
		}

		if( !$type ){
			return error( 'EMPTY_TYPE', '请选择要处理的举报');
		}

		$content = sInform::dealReport( $report_id, $this->_uid, $type);

		return $this->output_json(['result'=>'okay', 'msg' => $content ]);
	}
}
