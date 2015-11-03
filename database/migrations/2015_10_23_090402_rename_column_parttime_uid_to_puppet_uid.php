<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnParttimeUidToPuppetUid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function( $table ){
            $table->renameColumn('parttime_uid', 'puppet_uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function( $table ){
            $table->renameColumn('puppet_uid', 'parttime_uid');
        });
    }
}
