<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceIdToAsks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'asks', function( $table ){
            $table->integer('device_id')
                ->nullable()
                ->after('from')
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
        Schema::table( 'asks', function( $table ){
            $table->dropColumn('device_id');
        } );
    }
}
