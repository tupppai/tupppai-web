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
            $table->renameColumn('upload_ids', 'upload_id');
        });
    }
}
