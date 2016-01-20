<?php namespace App\Models;

class ThreadTag extends ModelBase{
    protected $table = 'thread_tags';
    protected $guarded = ['id'];

    public function scopeChecked( $query ){
        return $query->where('status', self::STATUS_CHECKED);
    }

    public function set_tag( $uid, $target_type, $target_id, $tag_id = 0, $status = 0, $reason = '' ){
        $data = [
            'target_type' => $target_type,
            'target_id' => $target_id
        ];
        if($tag_id) $data['tag_id'] = $tag_id;

        $thrdCat = $this->firstOrNew( $data );

        if( !$thrdCat->id ){
            $data['create_by'] = $uid;
        }
        $data['tag_id'] = $tag_id;
        $data['status'] = $status;
        $data['reason'] = $reason;
        $data['update_by'] = $uid;
        return $thrdCat->assign( $data )->save();
    }

    public function get_thread_user_count($tag_id) {
        return self::where('status', self::STATUS_NORMAL)
            ->where('tag_id', $tag_id)
            ->distinct()
            ->count('create_by');
    }

    public function get_thread_count($tag_id) {
        //return self::where('status', self::STATUS_NORMAL)
            //->distinct('target_id')
            //->distinct('target_type')
        return self::where('tag_id', $tag_id)
            ->count();
    }

    public function get_tag_ids_of_thread( $target_type, $target_id, $status = NULL){
        $cond = [
            'target_id' => $target_id,
            'target_type' => $target_type
        ];
        if( !is_null($status) ){
            $cond['status'] = $status;
        }

        $results = $this->where( $cond )->get();

        return $results;
    }

    public function get_valid_threads_by_tag( $tag_id, $page , $size ){
        $tcTable = $this->table;
        return $this->leftjoin('asks', function($join) use ( $tcTable ){
                        $join->on( $tcTable.'.target_id', '=', 'asks.id')
                            ->where($tcTable.'.target_type', '=', 1);
                    })
                    ->leftjoin('replies', function($join) use ( $tcTable ){
                        $join->on( $tcTable.'.target_id', '=', 'replies.id')
                            ->where($tcTable.'.target_type', '=', 2);
                    })
                    ->where(function($query){
                        $query->where(function($query){
                            $uid = _uid();
                            //加上自己的广告贴
                            $query = $query->where('replies.status','>', self::STATUS_DELETED );
                            if( $uid ){
                                $query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
                            }
                        })
                        ->orWhere(function($query){
                            $uid = _uid();
                            //加上自己的广告贴
                            $query = $query->where('asks.status','>', self::STATUS_DELETED );
                            if( $uid ){
                                $query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
                            }
                        });
                    })
                    ->where( $tcTable.'.tag_id', $tag_id)
                    ->valid()
                    //跟后台管理系统的时间保持一致
                    ->orderBy( $tcTable.'.update_time', 'DESC')
                    ->forPage( $page, $size )
                    ->select( $tcTable.'.*' )
                    ->get();
    }

    public function get_asks_by_tag( $tag_id, $status, $page, $size ){
        $tcTable = $this->table;
        if( !is_array( $status ) ){
            $status = [$status];
        }
        return $this->leftjoin('asks', function($join) use ( $tcTable ){
                        $join->on( $tcTable.'.target_id', '=', 'asks.id')
                            ->where($tcTable.'.target_type', '=', self::TYPE_ASK);
                    })
                    ->where(function($query){
                        $uid = _uid();
                        //加上自己的广告贴
                        $query = $query->where('asks.status','>', self::STATUS_DELETED );
                        if( $uid ){
                            $query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
                        }
                    })
                    ->where( $tcTable.'.tag_id', $tag_id )
                    ->whereIn( $tcTable.'.status', $status )
                    //跟后台管理系统的时间保持一致
                    ->orderBy( $tcTable.'.update_time', 'DESC')
                    ->forPage( $page, $size )
                    ->select( $tcTable.'.*' )
                    ->get();
    }

    public function get_valid_replies_by_tag( $tag_id, $page, $size ){
        $tcTable = $this->table;
        return $this->leftjoin('replies', function($join) use ( $tcTable ){
                        $join->on( $tcTable.'.target_id', '=', 'replies.id')
                            ->where($tcTable.'.target_type', '=', self::TYPE_REPLY);
                    })
                    ->where(function($query){
                        $uid = _uid();
                        //加上自己的广告贴
                        $query = $query->where('replies.status','>', self::STATUS_DELETED );
                        if( $uid ){
                            $query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
                        }
                    })
                    ->where( $tcTable.'.tag_id', $tag_id )
                    ->valid()
                    //跟后台管理系统的时间保持一致
                    ->orderBy( $tcTable.'.update_time', 'DESC')
                    ->forPage( $page, $size )
                    ->select( $tcTable.'.*' )
                    ->get();
    }

}
