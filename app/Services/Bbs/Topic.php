<?php namespace App\Services\Bbs;

use App\Services\ServiceBase;

use App\Models\Bbs\Topic as mTopic;

use App\Services\User as sUser;

use App\Facades\CloudCDN;

class Topic extends ServiceBase{
    public static function searchTopics($keyword, $page, $size) {
        $topics = (new mTopic)->search_topics($keyword, $page, $size);

        $data = array();
        foreach($topics as $topic) {
            $data = self::brief($topic);
            $data['content'] = strip_tags($data['content']);

            $user = sUser::getUserByUid($topic->uid);
            $data = array_merge($data, sUser::brief($user));
        }
        return $data;
    }

    public static function brief ( $data ) {
        $data = $data->toArray();

        return $data;
    }
}
