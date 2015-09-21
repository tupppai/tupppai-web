<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUploadIdToUploadIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asks', function( $table ){
            $table->string('upload_id', 255)->change();
            $table->renameColumn('upload_id', 'upload_ids');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'asks', function($table){
            $table->integer('upload_id',11)->change();    
            $table->renameColumn('uploads_ids', 'upload_id');
        });
    }
}
