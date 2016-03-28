<?php

namespace App\Models;

use App\Models\ThreadCategory as mThreadCategory;
use App\Models\ThreadTag as mThreadTag;

use DB;

//Just for ask+reply.
//there's no actual table in the db.
class Thread extends ModelBase
{
    protected $table = '';
    protected $cond = [
        'thread'         => [],
    	'thread_category'=> [],
    	'ask'            => [],
    	'reply'          => [],
    	'recommendation' => [],
        'user_role'      => [],
        'desc'           => [],
        'nickname'       => [],
        'thread_tag'     => []
    ];

    public function scopeType( $query, $type ){
        switch ($type) {
            case 'unreviewed':
                $this->cond['thread_category']['status'] = [ mThreadCategory::STATUS_CHECKED, mThreadCategory::STATUS_NORMAL ];
                $this->cond['thread_category']['category_id']   = [ mThreadCategory::CATEGORY_TYPE_POPULAR ];
                break;
            case 'app':
                $this->cond['thread_category']['status'] = [ mThreadCategory::STATUS_NORMAL, mThreadCategory::STATUS_READY, mThreadCategory::STATUS_REJECT ];
                $this->cond['thread_category']['category_id']   = [ mThreadCategory::CATEGORY_TYPE_APP_POPULAR ];
                break;
            case 'pc':
                $this->cond['thread_category']['status'] = [ mThreadCategory::STATUS_NORMAL, mThreadCategory::STATUS_READY, mThreadCategory::STATUS_REJECT ];
                $this->cond['thread_category']['category_id']   = [ mThreadCategory::CATEGORY_TYPE_PC_POPULAR ];
                break;
            case 'visible':
                $this->cond['thread']['status'] = [ mThreadCategory::STATUS_NORMAL, mThreadCategory::STATUS_DONE ];
                break;
            case 'all':
            default:
                break;
        }
        return $query;
    }

    public function scopeget_threads( $query, $page, $size, $filter_block = true ){
	$target_type = $this->cond['thread']['target_type'];
        $tcTable = (new mThreadCategory())->getTable();

        $asks   = DB::table('asks')->selectRaw('asks.id, 1 as type, asks.create_time, asks.update_time')
                    ->leftJoin( $tcTable, function( $join ) use ( $tcTable ) {
                        $join->on( 'asks.id', '=', $tcTable.'.target_id' )
                             ->where( 'target_type', '=', mThreadCategory::TYPE_ASK )
                             ->where( $tcTable.'.status', '!=', mThreadCategory::STATUS_DELETED);
                    })
                    ->leftjoin('users', 'users.uid', '=', 'asks.uid');
        $replies= DB::table('replies')->selectRaw('replies.id, 2 as type, replies.create_time, replies.update_time')
                    ->leftJoin( $tcTable, function( $join ) use ( $tcTable ) {
                        $join->on( 'replies.id', '=', $tcTable.'.target_id' )
                            ->where( 'target_type', '=', mThreadCategory::TYPE_REPLY )
                            ->where( $tcTable.'.status', '!=', mThreadCategory::STATUS_DELETED);
                    })
                    ->leftjoin('users', 'users.uid', '=', 'replies.uid');

        if( $filter_block ){
            $asks->where(function( $query ){
                $query = $query->where('asks.status','>', mThreadCategory::STATUS_DELETED )
                                ->orWhere(function($q){
                                    $q = $q->where('asks.uid', _uid())
                                            ->where('asks.status', mThreadCategory::STATUS_BLOCKED);
                                });
            });
            $replies->where(function( $query ){
                $query = $query->where('replies.status','>', mThreadCategory::STATUS_DELETED )
                                ->orWhere(function($q){
                                    $q = $q->where('replies.uid', _uid())
                                            ->where('replies.status', mThreadCategory::STATUS_BLOCKED);
                                });
            });
        }
        // dd($asks);

        $asks->where( 'category_id', '!=', mThreadCategory::CATEGORY_TYPE_TUTORIAL );
        if( isset( $this->cond['thread_category']['category_id'] ) ){
            $asks->wherein( 'category_id', $this->cond['thread_category']['category_id'] );
            $replies->wherein( 'category_id', $this->cond['thread_category']['category_id'] );
        }
        if( isset( $this->cond['thread_category']['status'] ) ){
            $asks->wherein( 'thread_categories.status', $this->cond['thread_category']['status'] );
            $replies->wherein( 'thread_categories.status', $this->cond['thread_category']['status'] );
        }

        if( isset( $this->cond['thread']['desc'] ) ) {
            $asks->where( 'asks.desc', 'like', '%'.$this->cond['thread']['desc'].'%' );
            $replies->where( 'replies.desc', 'like', '%'.$this->cond['thread']['desc'].'%' );
        }

        if( isset( $this->cond['thread']['uid'] ) ) {
            $asks->where( 'asks.uid', $this->cond['thread']['uid'] );
            $replies->where( 'replies.uid', $this->cond['thread']['uid'] );
        }
        if( isset( $this->cond['thread']['nickname'] ) ) {
            $asks->where( 'users.nickname', 'LIKE', '%'.$this->cond['thread']['nickname'].'%' );
            $replies->where( 'users.nickname', 'LIKE', '%'.$this->cond['thread']['nickname'].'%' );
        }
        if( isset( $this->cond['user_role']['role_id'] ) ) {
            $asks->leftjoin( 'user_roles', 'user_roles.uid', '=', 'users.uid')
                 ->where( 'user_roles.role_id', $this->cond['user_role']['role_id']  )
                 ->where('user_roles.status', mThreadCategory::STATUS_NORMAL );
            $replies->leftjoin( 'user_roles', 'user_roles.uid', '=', 'users.uid')
                    ->where( 'user_roles.role_id', $this->cond['user_role']['role_id']  )
                    ->where('user_roles.status', mThreadCategory::STATUS_NORMAL );
        }

        if( isset( $this->cond['recommendations']['role_id'] ) ){
        	$rec_status = $this->cond['recommendations']['status'];
        	$asks->rightjoin('recommendations', 'users.uid','=','recommendations.uid')
        		 ->where('recommendations.status', '=', $rec_status )
        		 ->where('recommendations.role_id', '=', $this->cond['recommendations']['role_id']);
			$replies->rightjoin('recommendations', 'users.uid','=','recommendations.uid')
				 ->where('recommendations.status', '=', $rec_status )
				 ->where('recommendations.role_id', '=', $this->cond['recommendations']['role_id']);
        }

        if( isset( $this->cond['thread']['status'] ) ) {
            $asks->whereIn( 'asks.status', $this->cond['thread']['status'] );
            $replies->whereIn( 'replies.status', $this->cond['thread']['status'] );
        }

        if( isset( $this->cond['thread']['target_id'] ) ) {
            $asks->where( 'asks.id', $this->cond['thread']['target_id'] );
            $replies->where( 'replies.id', $this->cond['thread']['target_id'] );
        }

        if( isset( $this->cond['thread_tag']['id'] ) ) {
            $tTTable = (new mThreadTag)->getTable();
            $asks->leftJoin( $tTTable, function( $join ) use ( $tTTable ) {
                        $join->on( 'asks.id', '=', $tTTable.'.target_id' )
                            ->where( $tTTable.'.target_type', '=', mThreadCategory::TYPE_ASK )
                            ->where( $tTTable.'.status', '!=', mThreadCategory::STATUS_DELETED);
                    })
                    ->whereIn( $tTTable.'.tag_id', $this->cond['thread_tag']['id'] );
            $replies->leftJoin( $tTTable, function( $join ) use ( $tTTable ) {
                        $join->on( 'replies.id', '=', $tTTable.'.target_id' )
                            ->where( $tTTable.'.target_type', '=', mThreadCategory::TYPE_REPLY )
                            ->where( $tTTable.'.status', '!=', mThreadCategory::STATUS_DELETED);
                    })
                    ->whereIn( $tTTable.'.tag_id', $this->cond['thread_tag']['id'] );
        }

        //count all
        $total = $asks->count() + $replies->count();

        if( in_array('ask', $target_type ) ){
            $threads = $asks;
        }
        if( in_array('reply', $target_type ) ){
			$threads = $replies;
        }
        if( empty( array_diff(['ask','reply'], $target_type ) ) ){
        	$threads = $asks->union($replies);
        }

        //get result
        $result = $threads
            ->orderBy('create_time','DESC')
            ->forPage( $page, $size )
            ->get();

        return ['result' => $result, 'total' => $total ];
    }

    public function scopeTargetType( $query, $target_type ){
		if( !is_array( $target_type ) ){
			$target_type = [$target_type];
		}
		if( $target_type[0] == 'all' || is_null( $target_type ) ){
			$target_type = ['ask','reply'];
		}

		$this->cond['thread']['target_type'] = $target_type;
    	return $query;
    }

    public function scopeThreadType( $query, $thread_type ){
        switch( $thread_type ){
            case 'hot':
                $this->cond['thread_category']['category_id'] = [ mThreadCategory::CATEGORY_TYPE_POPULAR ];
                $this->cond['thread_category']['status'] = [ mThreadCategory::STATUS_CHECKED, mThreadCategory::STATUS_NORMAL ];
                break;
            case 'blocked':
                $this->cond['thread']['status'] = [ mThreadCategory::STATUS_BLOCKED ];
                break;
            case 'all':
                //$this->cond['thread']['status'] = array_merge(range(-6,-1), range(1,2));
            default:
                break;
        }

        return $query;
    }

    public function scopeUserType( $query, $user_type ){
        switch( $user_type ){
            case 'stars':
                $this->cond['recommendations']['role_id'][] = mThreadCategory::ROLE_STAR    ;
                $this->cond['recommendations']['status'][]  = mThreadCategory::STATUS_NORMAL;
                break;
            case 'rec_stars':
                $this->cond['recommendations']['role_id'][] = mThreadCategory::ROLE_STAR     ;
                $this->cond['recommendations']['status'][]  = mThreadCategory::STATUS_CHECKED;
                break;
            case 'blacklist':
                $this->cond['recommendations']['role_id'][] = mThreadCategory::ROLE_BLACKLIST;
                $this->cond['recommendations']['status'][]  = mThreadCategory::STATUS_NORMAL ;
                break;
            case 'rec_blacklist':
                $this->cond['recommendations']['role_id'][] = mThreadCategory::ROLE_BLACKLIST;
                $this->cond['recommendations']['status'][]  = mThreadCategory::STATUS_CHECKED;
                break;
            case 'all':
            default:
                break;
        }

        return $query;
    }
    public function scopeUserRole( $query, $user_role ){
        switch( $user_role ){
            case 'newbie':
                $this->cond['user_role']['role_id'] = mThreadCategory::ROLE_NEWBIE   ;
                break;
            case 'general':
                $this->cond['user_role']['role_id'] = mThreadCategory::ROLE_GENERAL  ;
                break;
            case 'trustable':
                $this->cond['user_role']['role_id'] = mThreadCategory::ROLE_TRUSTABLE;
                break;
            case 'blocked':
                $this->cond['user_role']['role_id'] = mThreadCategory::ROLE_BLOCKED  ;
                break;
            default:
                break;
        }

        return $query;
    }

    public function scopeUid( $query, $uid ){
    	if( $uid ){
    		$this->cond['thread']['uid'] = $uid;
    	}

    	return $query;
    }

    public function scopeNickname( $query, $nickname){
    	if( $nickname ){
    		$this->cond['thread']['nickname'] = $nickname;
    	}

    	return $query;
    }

    public function scopeThreadId( $query, $target_id ){
    	if( $target_id ){
    		$this->cond['thread']['target_id'] = $target_id;
    	}

    	return $query;
    }

    public function scopeDesc( $query, $desc ){
    	if( $desc ){
    		$this->cond['thread']['desc'] = $desc;
    	}

    	return $query;
    }

    public function scopeCategories( $query, $ids ){
        if( is_string( $ids ) ){
            $ids = explode(',', $ids );
        }
        if( is_int( $ids ) ){
            $ids = [ $ids ];
        }
        if( is_array( $ids ) ){
            $ids = array_unique( $ids );
        }
        if( $ids ){
            $this->cond['thread_category']['category_id'] = $ids;
            // $this->cond['thread_category']['status'] = [ mThreadCategory::STATUS_NORMAL];
        }
        return $query;
    }

    public function scopeTags( $query, $tag_ids ){
        if( is_string( $tag_ids ) ){
            $tag_ids = explode(',', $tag_ids );
        }
        if( is_int( $tag_ids ) ){
            $tag_ids = [ $tag_ids ];
        }
        if( is_array( $tag_ids ) ){
            $tag_ids = array_unique( $tag_ids );
        }
        if( $tag_ids ){
            $this->cond['thread_tag']['id'] = $tag_ids;
            // $this->cond['thread_category']['status'] = [ mThreadCategory::STATUS_NORMAL];
        }
        return $query;
    }
}
