<?php
	namespace App\Models;

	class Puppet extends ModelBase{
		protected $table = 'puppets';
		public $timestamps = false;
		protected $guarded = [];

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

		public function get_puppets( $uid ){
			return $this->with( 'user' )
						->where( 'owner_uid', $uid )
						->orderBy( 'puppet_uid', 'DESC' )
						->get();
		}
	}
