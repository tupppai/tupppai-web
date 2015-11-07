<?php namespace App\Services\Bbs;

use App\Services\ServiceBase;
use App\Models\Bbs\Topic as mTopic;
use App\Facades\CloudCDN;

class Topic extends ServiceBase{
    public static function searchTopics($keyword, $page, $size) {
        return (new mTopic)->search_topics($keyword, $page, $size);
    }
}
