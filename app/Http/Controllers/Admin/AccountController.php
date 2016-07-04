<?php namespace App\Http\Controllers\Admin;

	use App\Models\UserLanding as mUserLanding;

	use App\Services\SysMsg as sSysMsg;
	use App\Services\User as sUser;
	use App\Services\UserLanding as sUserLanding;

	use App\Trades\User as tUser;
	use App\Trades\Transaction as tTransaction;
	use App\Trades\Account as tAccount;

	use App\Facades\CloudCDN;
	use App\Facades\EasyWeChat;

	use App\Jobs\Push;
	use Queue;

	class AccountController extends ControllerBase{
		public function rechargeAction(){
			return $this->output();
		}

		public function recharge_for_usersAction( ){
			$uids = $this->post('uids', 'string');
			$amount = $this->post('amount', 'money');
			$uids = explode(',', $uids);

			foreach( $uids as $uid ){
				tUser::pay( tUser::SYSTEM_USER_ID, $uid, $amount );
			}

			return $this->output_json(['result'=>'ok']);
		}

		public function transactionsAction(){
			return $this->output();
		}
		public function list_transactionsAction(){
	        $account = new tAccount();

	        $uid = $this->post("uid", "int");

	        $cond = [];
	        $cond['uid'] = $uid;

			$data  = $this->page($account, $cond, array(), ['id DESC']);
			$unit = config('global.TRANS_UNIT');

			foreach( $data['data'] as $row ){
				$user = sUser::detail( sUser::getUserByUid( $row->uid ) );
				$row->userinfo = $user['username'].'(uid:'.$row->uid.')';
				switch( $row->type ){
					case tAccount::TYPE_INCOME:
						$row->type = '收入';
						break;
					case tAccount::TYPE_OUTCOME:
						$row->type = '支出';
						break;
					case tAccount::TYPE_FREEZE:
						$row->type = '冻结';
						break;
					case tAccount::TYPE_UNFREEZE:
						$row->type = '解冻';
						break;
				}
				switch ( $row->status ) {
					case tAccount::STATUS_NORMAL:
						$row->status = '成功';
						break;
					case tAccount::STATUS_FAILED:
						$row->status = '失败';
						break;
					case tAccount::STATUS_DELETED:
						$row->status = '取消';
						break;
					default:
						# code...
						break;
				}

				$row->trans_balance = number_format( $row->balance, 2 ).$unit;
				$row->trans_amount = number_format( $row->amount, 2 ).$unit;
			}
			return $this->output_table( $data );
		}

		public function transfer_to_userAction(){
			$amount = $this->post('amount', 'money', 0);
	        $from_uid = $this->post('from_uid', 'int');
	        $to_uid = $this->post('to_uid', 'int');
	        $reason = $this->post('reason', 'string');

	        if( !sUser::checkUserExistByUid($from_uid) ){
				return error('USER_NOT_EXIST', '来源用户不存在');
	        }
	        if( !sUser::checkUserExistByUid($to_uid) ){
				return error('USER_NOT_EXIST', '目标用户不存在');
	        }

			tUser::pay( $from_uid, $to_uid, $amount, $reason );

			return $this->output_json(['result'=>'ok']);
		}

		public function update_withdrawAction(){
			$tid = $this->get('trade_id', 'int');
			$status = $this->get( 'status', 'string');
			$reason = $this->get( 'reason', 'string');
			if( !$status ){
				return error('WRONG_ARGUMENTS', '请选择是否允许提现');
			}
			$pingp = [];
			$result = 'ok';
			$msg = '';
			$trade = tTransaction::find( $tid );
			if( $status == 'approve' ){
				$pingp = tAccount::red( $tid );
				sSysMsg::postMsg( 0, '您的提现请求已通过。', 0, 0, '', date('Y-m-d H:i:s'), $trade->uid, 'withdraw_success', '' );
				if( $pingp->status == 'failed' ){
					$result = 'failed';
					$msg = $pingp->failure_msg;
					//交易状态在red里已经被改成failed了，所以审核那里拉不出来
					tAccount::refuse( $tid, '系统余额不足' );
				}
			}
			else if( $status == 'refuse' ){
				$pingp = tAccount::refuse( $tid, '拒绝理由：'.$reason );
				sSysMsg::postMsg( 0, '您的提现请求已被拒绝。拒绝理由：'.$reason, 0, 0, '', date('Y-m-d H:i:s'), $trade->uid, 'withdraw_refuse', '' );
				Queue::push( new Push([
					'type' => 'withdraw_refuse',
					'uid' => $pingp->uid,
					'reason' => $reason
				]));
			}

            return $this->output([ 'result' => $result, 'msg' => $msg]);
		}

		public function check_withdrawAction(){
			return $this->output();
		}

		public function list_withdrawsAction(){
			$transactions = new tTransaction();

	        $uid = $this->post("uid", "int");
	        $status = $this->post('status', 'string', 'pending');

	        switch ($status) {
				case 'pending':
					$status = tTransaction::STATUS_PENDING;
					break;

				default:
					# code...
					break;
	        }

	        $cond = [];
	        $cond['uid'] = $uid;
	        $cond['trade_status'] = $status;

			$data  = $this->page($transactions, $cond, array(), ['id DESC']);

			foreach ($data['data'] as $row) {
				$user = sUser::getUserByUid( $row->uid );
				$row->nickname = $user->nickname;

				$oper = [];
				switch ( $row->trade_status ) {
					case tTransaction::STATUS_DELETED:
						$row->trade_status = '取消';
						break;
					case tTransaction::STATUS_NORMAL:
						$row->trade_status = '成功';
						break;
					case tTransaction::STATUS_PAYING:
						$row->trade_status = '支付中';
						break;
					case tTransaction::STATUS_TIMEOUT:
						$row->trade_status = '超时';
						break;
					case tTransaction::STATUS_UNCERTAIN:
						$row->trade_status = '不确定';
						break;
					case tTransaction::STATUS_PENDING:
						$row->trade_status = '待审核';
						$oper[] = '<a href="#" class="check_withdraw" data-status="approve">允许</a>';
						$oper[] = '<a href="#" class="check_withdraw" data-status="refuse">拒绝</a>';
						break;
					case tTransaction::STATUS_FAILED:
						$row->trade_status = '失败';
						break;
					default:
						$row->trade_status = '未知';
						break;
				}

				switch ( $row->payment_type ) {
					case tTransaction::PAYMENT_TYPE_CASH:
						$row->payment_type = '现金';
						break;
					case tTransaction::PAYMENT_TYPE_WECHAT:
						$row->payment_type = '微信';
						break;
					case tTransaction::PAYMENT_TYPE_WECHAT_RED:
						$row->payment_type = '微信红包';
						break;
					case tTransaction::PAYMENT_TYPE_WECHAT_TRANSFER:
						$row->payment_type = '微信转账';
						break;
					case tTransaction::PAYMENT_TYPE_ALIPAY:
						$row->payment_type = '支付宝';
						break;
					case tTransaction::PAYMENT_TYPE_UNION:
						$row->payment_type = '银联卡';
						break;
					case tTransaction::PAYMENT_TYPE_CREDIT:
						$row->payment_type = '信用卡';
						break;
					default:
						$row->payment_type = '未知';
						break;
				}
				$row->amount = number_format( $row->amount/100 , 2 );
				$row->oper = implode(' / ', $oper);
			}
			return $this->output_table( $data );
		}

		public function search_valid_usersAction(){
	        $q = $this->get('q','string');
	        if( empty( $q )){
	            return error('EMPTY_QUERY_STRING');
	        }

	        $users = sUser::getValidUsersByFuzzyIdAndName( $q );

	        foreach( $users as $key => $user){
	            $user->avatar = CloudCDN::file_url($user->avatar);
	        }

	        return $this->output_json($users);
		}

		public function upload_merge_user_listAction(){
			return $this->output();
		}

		public function merge_userAction(){
			$uids = $this->post( 'merge_uids', 'string' );
			if( !$uids ){
				return error( 'WRONG_ARGUMENTS', '请粘贴要合并的uid列表' );
			}

			$merge_uids = explode("\n", $uids );
			foreach ($merge_uids as $uid) {
				$uid = explode(' ', $uid );
				$from_uid = $uid[0];
				$to_uid = $uid[1];

				sUser::mergeUser( $from_uid, $to_uid );
			}

			return $this->output(['result'=>'ok']);
		}

	    public function completeUnionIdAction(){
	        return $this->output();
	    }

	    public function completeUIDAction(){

			//TYPE_WEIXIN_MP
			//get empty unionid wx users
			$user_landings = sUserLanding::getEmptyUnionIdByType(mUserLanding::TYPE_WEIXIN_MP);
			//for each user, get union id
			$app = EasyWeChat::getFacadeRoot();
			foreach( $user_landings as $user_landing ){
				try{
					$userinfo = $app->user->get( $user_landing->openid );
				}
				catch( \Exception $e ){
					continue;
					//invalid openid hint
					//用户不再关注我们了 或 openid不是WEIXIN_MP这个公众号的
				}
				$unionid = $userinfo->unionid;
				sUserLanding::updateUserUnionIdById( $user_landing->id, $userinfo->unionid );
			}

			//TYPE_WEIXIN
			//for type WEIXIN_MP 5
			// $ewcConfigs = config('wechat');
			// $ewcConfigs['app_id'] = env('WX_APPID_CPLM');
			// $ewcConfigs['secret'] = env('WX_APPSECRET_CPLM');
			// $ewcConfigs['token']  = env('WX_TOKEN_CPLM');
	  //       $wechat = app('EasyWeChat\\Foundation\\Application');
	  //       $EWC = new $wechat($ewcConfigs);
			// //get empty unionid wx users
			// $user_landings = sUserLanding::getEmptyUnionIdByType(mUserLanding::TYPE_WEIXIN);
			// //for each user, get union id
			// foreach( $user_landings as $user_landing ){
			//	// try{
			//		$userinfo = $EWC->user->get( $user_landing->openid );
			//	// }
			//	// catch( \Exception $e ){
			//		continue;
			//		//invalid openid hint
			//		//用户不再关注我们了
			//	// }
			//	$unionid = $userinfo->unionid;
			//	sUserLanding::updateUserUnionIdById( $user_landing->id, $userinfo->unionid );
			// }
			return $this->output(['result' => 'ok']);
	    }


	}
