<?php

namespace App\Models;

class Download extends ModelBase
{
    protected $table = 'downloads';
    protected $fillable = ['uid', 'type','target_id','status','ip','update_time','create_time','url'];

    public function get_download_record_by_id( $id ){
        return $this->find($id);
        //return $this->where( [ 'id' => $id ] )->first();
    }

    public function get_download_record( $uid, $target_id, $status = 1){
        return $this->where([
            'uid' => $uid,
            'type' => self::TYPE_ASK,
            'target_id' => $target_id,
            'status' => $status
        ])->first();
    }

    public function get_ask_downloaded($uid, $channel_id, $page, $size, $last_updated) {
        $query = $this->where( [
                'downloads.uid'=> $uid,
                'downloads.status' => self::STATUS_NORMAL
            ])
            ->where( 'downloads.type', self::TYPE_ASK)
            ->leftjoin( 'asks', 'asks.id', '=', 'downloads.target_id')
            //bugfix for 进行中看不到－6的求助
            ->blocking($uid)
            /*->where( function( $query ) use ( $uid ){
                $query->where( 'asks.status', '>', self::STATUS_DELETED );
                if( $uid == _uid() ){
                    $query->orwhere( 'asks.uid', $uid )
                        ->where('asks.status', self::STATUS_BLOCKED );
                }
            })*/
            ->where( 'downloads.update_time', '<', $last_updated );
            if( $channel_id ){
                $query = $query->where('category_id', $channel_id);

                //todo: remove
                // $query->leftjoin('thread_categories', function( $join ) use ( $channel_id ){
                //     $join->on( 'thread_categories.target_id', '=', 'asks.id')
                //         ->where('thread_categories.target_type', '=', self::TYPE_ASK);
                // })
                // ->where( 'thread_categories.category_id', '=', $channel_id );
            }

        return $query->orderBy('downloads.create_time', 'desc')
            ->select('downloads.*')
            ->forPage( $page, $size )
            ->get();
    }

    public function get_downloaded( $uid, $page, $size, $last_updated ){
        return $this->where( [
                'uid'=> $uid,
                'status' => self::STATUS_NORMAL
            ])
            ->where( 'update_time', '<', $last_updated )
            ->orderBy('create_time', 'desc')
            ->forPage( $page, $size )
            ->get();
    }

    public function get_done( $uid, $page, $size, $last_updated, $category_id ){
        return $this->where( [
                'uid'=> $uid,
                'status' => self::STATUS_HIDDEN
            ])
            ->where(function($query) use ( $category_id ){
                if(!is_null($category_id)){
                    $query->where( 'category_id', $category_id );
                }
            })
            ->where( 'update_time', '<', $last_updated )
            ->orderBy( 'update_time', 'DESC' )
            ->groupBy([ 'type', 'target_id', 'category_id' ])
            ->forPage( $page, $size )
            ->get();
    }

    public function get_first_record_by_target( $uid, $target_type, $target_id, $category_id ){
        return $this->where( [
                'uid'=> $uid,
                'type' => $target_type,
                'target_id' => $target_id,
                'category_id' => $category_id
            ])
            ->orderBy('create_time', 'DESC')
            ->orderBy('status', 'ASC')
            ->first();
    }
    public function get_ask_downloaded_users($ask_id, $page, $size) {
        return $this->where('target_id', $ask_id)
            ->where('type', self::TYPE_ASK)
            ->leftjoin( 'users', 'users.uid', '=', 'downloads.uid')
            ->forPage($page, $size)
            ->get();
    }

    public function count_user_download( $uid, $target_type = NULL, $status = NULL ){
        $builder = $this->where( 'downloads.uid', $uid )
                    ->where(function( $query ) use ( $status ){
                        if( !is_null( $status ) ){
                            $query->where( 'downloads.status', self::STATUS_NORMAL );
                        }
                    })
                    ->where( function( $query ) use ( $target_type ){
                        if( !is_null( $target_type ) ){
                            $query->where( 'downloads.type', $target_type );
                        }
                    });

        switch( $target_type ){
            case self::TYPE_ASK:
                $builder = $builder->leftjoin( 'asks', 'asks.id', '=', 'downloads.target_id')
                      ->where('asks.status', '>', self::STATUS_DELETED );
                break;
            case self::TYPE_REPLY:
                $builder = $builder->leftjoin( 'asks', 'asks.id', '=', 'replies.ask_id')
                      ->where('asks.status', '>', self::STATUS_DELETED)
                      ->orwhere(function( $query ) use ( $uid ){
                            if( $uid == _uid() ){
                                $query->where( 'asks.uid', $uid )
                                      ->where( 'asks.status', self::STATUS_BLOCKED);
                            }
                      })
                      ->leftjoin( 'replies', 'replies.id', '=', 'downloads.target_id')
                      ->where( 'replies.status', '>', self::STATUS_DELETED)
                      ->orwhere( function( $query ) use ( $uid ){
                        if( $uid  == _uid() ){
                            $query->where( 'replies.uid', $uid )
                                  ->where( 'replies.status', self::STATUS_BLOCKED);
                        }
                      });
                break;
        }
        return $builder->count();
    }


    /**
    * 分页方法
    */
    public function page($keys = array(), $page, $limit)
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, $v);
        }
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 计算作品的下载数量
     */
    public function count_reply_download($reply_id) {
        $count = self::where('target_id', $reply_id)
            ->where('type', self::TYPE_REPLY)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }
    public function count_ask_download($ask_id) {
        $count = self::where('target_id', $ask_id)
            ->where('type', self::TYPE_ASK)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 获取下载数量
     */
    public function count_download($type, $id) {
        $count = self::where('target_id', $id)
            ->where('type', $type)
            //->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 判断用户是否下载过
     */
    public function has_downloaded($uid, $type, $target_id, $category_id) {
        return self::where('type', $type)
            ->where('uid', $uid)
            ->where('target_id', $target_id)
            ->where('category_id', $category_id)
            ->exists();
    }

    public function is_in_progress( $uid, $type, $target_id, $category_id ){
        return self::where('type', $type)
            ->where('uid', $uid)
            ->where('target_id', $target_id)
            ->where('category_id', $category_id)
            ->where('status', self::STATUS_NORMAL)
            ->exists();
    }
}
