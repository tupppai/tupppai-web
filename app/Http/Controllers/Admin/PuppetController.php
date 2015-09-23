<?php
	namespace App\Http\Controllers\Admin;
	use App\Services\Puppet as sPuppet;
	use App\Services\ActionLog as sActionLog;

	class PuppetController extends ControllerBase{
		public function indexAction(){
        	return $this->output();
		}

		public function list_puppetsAction(){
			$cond = array();
	        $nickname = $this->post("nickname", "string");
	        $uid = $this->post("uid", "string");

	        if( $nickname ){
	        	$cond['nickname']   = $nickname;
	        }
	        if( $uid ){
				$cond['uid']   = $uid;
	        }

	        $uid = $this->_uid;
	        $data = sPuppet::getPuppetList( $this->_uid, $cond );

        	return $this->output_table( $data );
		}

		public function get_puppetsAction(){
			$puppets = sPuppet::getPuppets( $this->_uid );

			return $this->output_json( $puppets );
		}

		public function edit_profileAction(){
			$uid = $this->post( 'uid', 'int', 0 );
			$nickname = $this->post( 'nickname', 'string' );
			$gender = $this->post( 'sex', 'int' );
			$avatar = $this->post( 'avatar', 'string' );
			$phone = $this->post( 'phone', 'string' );

			if( !$nickname ){
				return error( 'EMPTY_NICKNAME', '请输入昵称' );
			}
			if( is_null( $gender ) ){
				return error( 'EMPTY_SEX' ,'请选择性别' );
			}
			if( !$avatar ){
				return error( 'EMPTY_AVATAR', '请上传头像' );
			}
			$data = [
				'nickname' => $nickname,
				'username' => $nickname,
				'sex' => $gender,
				'avatar' => $avatar,
				'password' => '',
				'phone' => $phone
			];



			$user = sPuppet::editProfile( $this->_uid, $uid, $data );
			$rel = sPuppet::updatePuppetRelationOf( $this->_uid, $user->uid );

	        return $this->output( ['result' => 'ok'] );
	    }
	}
