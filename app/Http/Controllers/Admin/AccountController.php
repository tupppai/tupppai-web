<?php namespace App\Http\Controllers\Admin;

	use App\Services\User as sUser;
	use App\Trades\User as tUser;
	use App\Trades\Account as tAccount;

	use App\Jobs\Push;
	use Queue;

	class AccountController extends ControllerBase{
		public function rechargeAction(){
			$users = sUser::getValidUsers();
			return $this->output(['users' => $users ]);
		}

		public function recharge_for_usersAction( ){
			$uids = $this->post('uids', 'int');
			$amount = $this->post('amount', 'float');

			foreach( $uids as $uid ){
				tUser::pay( tUser::SYSTEM_USER_ID, $uid, $amount );
				Queue::push(new Push(array(
					'uid'=>$uid,
					'from_uid'=> tUser::SYSTEM_USER_ID,
					'type'=>'system_recharge',
					'amount' => money_convert( $amount )
				)));
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
	}
