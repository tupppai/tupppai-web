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
            $table->renameColumn('upload_id', 'upload_ids');
            $table->string('upload_ids', 255)->change();
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'asks', function(){
            $table->integer('upload_ids',11)->change();    
            $table->renameColumn('uploads_ids', 'upload_id');
        });
    }
}
