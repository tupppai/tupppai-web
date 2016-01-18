<?php namespace App\Http\Controllers\Admin;

	use App\Services\User as sUser;
	use App\Trades\User as tUser;
	use App\Trades\Account as tAccount;
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
			return $this->output_table( $data );
		}
	}
