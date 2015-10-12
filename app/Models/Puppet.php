<?php
	namespace App\Models;

	class Puppet extends ModelBase{
		protected $table = 'puppets';
		protected $guarded = [];

		public function userrole(){
			return $this->hasMany('App\Models\UserRole', 'uid', 'puppet_uid');
		}
		public function user(){
			return $this->belongsTo('App\Models\User', 'puppet_uid', 'uid');
		}


		public function list_puppets( $owner, $cond ){
			$data = $this::whereHas( 'user',function( $q ) use ($cond){
						if( isset( $cond['nickname'] ) ){
							$q->where('nickname', 'like', '%'.$cond['nickname'].'%' );
						}
						if( isset( $cond['uid'] ) ){
							$q->where('uid', $cond['uid'] );
						}
					})
					->where( 'owner_uid', $owner )
					->orderBy( 'puppet_uid', 'DESC' )
					->paginate( config('global.app.DEFAULT_PAGE_SIZE') );

			return  $data;
		}

		public function get_puppets( $uid, $roles = [] ){
			return $this->whereHas( 'userrole', function( $q) use ($roles){
							if( !$roles ){
								return;
							}
							$q->whereIn('role_id', $roles);
						})
						->where( 'owner_uid', $uid )
						->orderBy( 'puppet_uid', 'DESC' )
						->get();
		}
	}
