<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AsksIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asks', function( $table ){
            //todo in好像会中断,需要优化下
           $table->index('create_time');
            $table->index(['id','uid','create_time','update_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asks', function( $table ){
            $table->dropIndex('create_time');
            $table->dropIndex(['id','uid','create_time','update_time']);
        });
    }
}
