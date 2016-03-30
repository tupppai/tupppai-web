<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RewardAddTargetTypeIdRemoveAskid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rewards', function( $table ){
            $table->integer('target_type')->after('uid');
            $table->integer('target_id')->after('uid');
            $table->dropColumn('askid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rewards', function( $table ){
            $table->dropColumn('target_type');
            $table->dropColumn('target_id');
            $table->integer('askid')->after('uid');

        });
    }
}
