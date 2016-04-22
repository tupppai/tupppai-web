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
            $table->dropIndex(['status','target_type','category_id']);
            $table->dropIndex('target_id');
            //todo in好像会中断,需要优化下
           $table->index(['status','target_type','category_id']);
           $table->index('target_id');
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
            $table->dropIndex('target_id');
        });
    }
}
