<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counts', function( $table ){
            //todo in好像会中断,需要优化下
           $table->index(['action','uid','type','target_id','status']);
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
        Schema::table('counts', function( $table ){
            $table->dropIndex(['action','uid','type','target_id','status']);
            $table->dropIndex('target_id');
        });
    }
}
