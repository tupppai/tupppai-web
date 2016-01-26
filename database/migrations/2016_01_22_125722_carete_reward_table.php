<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CareteRewardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->integer('askid');
            $table->bigInteger('amount',false,true);
            $table->integer( 'create_time' );
            $table->integer( 'update_time' );

            $table->index(['uid','askid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rewards');
    }
}
