<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RepliesIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replies', function( $table ){
            //todo in好像会中断,需要优化下
           $table->index('status');
           $table->index('create_time');
           $table->index(['uid','create_time','update_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replies', function( $table ){
            $table->dropIndex('status');
            $table->dropIndex('create_time');
            $table->dropIndex(['uid','create_time','update_time']);
        });
    }
}
