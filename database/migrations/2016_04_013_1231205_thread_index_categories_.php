<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ThreadIndexCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thread_categories', function( $table ){
            //todo in好像会中断,需要优化下
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
            $table->dropIndex(['target_type','category_id']);
        });
    }
}
