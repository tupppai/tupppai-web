<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdsForReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'reviews', function( $table ){
            $table->string( 'category_ids' )
                  ->nullable()
                  ->after('labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'reviews', function( $table ){
            $table->dropColumn( 'category_ids' );
        });
    }
}
