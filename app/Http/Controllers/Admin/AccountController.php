<?php namespace App\Http\Controllers\Admin;

	use App\Services\User as sUser;
	class AccountController extends ControllerBase{
		public function rechargeAction(){
			$users = sUser::getValidUsers();
			return $this->output(['users' => $users ]);
		}

		public function recharge_for_usersAction( ){
			$uids = $this->post('uids', 'int');
			$amount = $this->post('amount', 'float');


		}
	}
