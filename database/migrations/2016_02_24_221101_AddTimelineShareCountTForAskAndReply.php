<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimelineShareCountTForAskAndReply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asks', function( $table ){
            $table->integer('timeline_share_count')
                    ->default(0)
                    ->after('weixin_share_count');
        });
        Schema::table('replies', function( $table ){
            $table->integer('timeline_share_count')
                    ->default(0)
                    ->after('weixin_share_count');
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
            $table->dropColumn('timeline_share_count');
        });
        Schema::table('replies', function( $table ){
            $table->dropColumn('timeline_share_count');
        });
    }
}
