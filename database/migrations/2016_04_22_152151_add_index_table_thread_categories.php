<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexTableThreadCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thread_categories', function( $table ){

            $table->index(['status','target_type','category_id']);
            $table->index('target_id');
            $table->index(['target_type','category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thread_categories', function( $table ){
            $table->dropIndex(['status','target_type','category_id']);
            $table->dropIndex(['target_id']);
            $table->dropIndex(['target_type','category_id']);
        });
    }
}
