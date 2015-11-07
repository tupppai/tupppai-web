<?php namespace App\Models\Bbs;

use App\Models\ModelBase;

class Topic extends ModelBase
{
    protected $connection = 'db_bbs';
    protected $table      = 'topics';

    
    public function search_topics($keyword, $page, $size){
        $topics = $this->where('title', 'LIKE', '%'.$keyword.'%')
            ->where('content', 'LIKE', '%'.$keyword.'%')
            ->forPage($page, $size)
            ->get();

        return $topics;
    }   
}
