<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceIdToReplies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'replies', function( $table ){
            $table->integer('device_id')
                ->nullable()
                ->after('type')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'replies', function( $table ){
            $table->dropColumn('device_id');
        } );
    }
}
