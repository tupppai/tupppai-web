<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class modify_primary_key extends Migration
{
    public function up()
    {
        
        Schema::table( 'users_use_devices', function( $table ){
            $table->dropColumn( 'primary key' );
            $table->integer( 'id', 'primary key' );
        });       
    }

    public function down()
    {
    }
}
