<?php namespace App\Http\Controllers\Main2;

use App\Services\User as sUser;
use App\Services\ThreadCategory as sThreadCategory;
use App\Services\Reply as sReply;
use App\Services\Ask as sAsk;
use App\Services\Category as sCategory;
use App\Services\Thread as sThread;

use App\Models\Reply as mReply;
use App\Models\Ask as mAsk;
use App\Models\ThreadCategory as mThreadCategory;

class CategoryController extends ControllerBase{
    public function show( $category_id ){
        $category = sCategory::detail( sCategory::getCategoryById( $category_id ) );

        return $this->output($category);
    }

    public function channels(){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 10);

        $cats = sCategory::getCategories( 'channels', 'valid', $page, $size );
        $categories    = [];
        foreach($cats as $key => $category) {
            $categories[] = sCategory::detail( $category );
        }

        return $this->output( $categories );
    }

    public function asks($category_id){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);

        $thCats = sThreadCategory::getThreadsByCategoryId( $category_id, mThreadCategory::TYPE_ASK, $page, $size );
        $asks = [];
        foreach ($thCats as $thCat) {
            $ask = sAsk::getAskById( $thCat->target_id );
            $asks[] = sAsk::detail( $ask );
        }

        return $this->output( $asks );
    }

    public function replies( $category_id ){
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);

        $asks = sThreadCategory::getCompletedAsksByCategoryId( $category_id, $page, $size );

        $threads = [];
        foreach( $asks as $ask ){
            $thread = [];
            $thread['ask'] = sAsk::detail( sAsk::getAskById( $ask->id ) );
            $thread['replies'] = sReply::getRepliesByAskId($ask->id, 1, 3);
            $threads[] = $thread;
        }

        return $this->output( $threads );
    }
}
