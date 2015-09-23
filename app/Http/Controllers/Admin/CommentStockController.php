<?php namespace App\Http\Controllers\Admin;

use App\Services\CommentStock as sCommentStock;

class CommentStockController extends ControllerBase{

    public function indexAction(){
        return $this->output();
    }

    public function list_commentsAction(){


        $cond = array();
        $content = $this->post('content', 'string');

        if( $content ){
            $cond['content']   = $content;
        }

        $data = sCommentStock::getCommentStock( $this->_uid, $cond );

        return $this->output_table( $data );
    }

    public function get_commentsAction(){
        $data = sCommentStock::getComments( $this->_uid );
        return $this->output_json( $data );
    }

    public function addCommentsAction(){
        $comments = $this->post( 'comments', 'string' );
        $comments = array_unique( array_filter( $comments ) );

        $results = sCommentStock::addComments( $this->_uid, $comments );

        return $this->output( ['result'=>'ok'] );
    }


    public function deleteCommentsAction(){
        $cids = $this->post("cids", "string");

        $c = sCommentStock::deleteComments( $this->_uid, $cids );

        return $this->output( ['result'=>'ok'] );
    }

}
