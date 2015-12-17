<?php
namespace App\Services;

use App\Models\Label as mLabel;

use App\Services\Reply as sReply;
use App\Services\Ask as sAsk;
use App\Services\Comment as sComment;

class ServiceBase {
 
    public static function parse($type, $id) {
        switch( $type ){
        case mlabel::TYPE_REPLY:
            $reply = sReply::getReplyById($id) ;

            return sReply::detail( $reply );
        case mLabel::TYPE_ASK:
            $ask = sAsk::getAskById( $id );

            return sAsk::detail( $ask );
        case mLabel::TYPE_COMMENT:
            $comment = sComment::getCommentById($id);

            return sComment::detail($comment);
        }
    }   
}
