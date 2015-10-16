<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPrimaryKey extends Migration
{
    public function up()
    {

        Schema::table( 'users_use_devices', function( $table ){
	       $table->dropPrimary();
           $table->dropIndex('device_id');
        });
    }

    public function down()
    {
        Schema::table( 'users_use_devices', function( $table ){
            $table->index('device_id', 'device_id');
        });
    }
}
