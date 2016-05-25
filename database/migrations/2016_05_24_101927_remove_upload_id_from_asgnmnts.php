<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUploadIdFromAsgnmnts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_parttime')->table( 'assignments', function( $table ){
            $table->dropColumn( 'upload_time' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_parttime')->table( 'assignments', function( $table ){
            $table->integer( 'upload_time' )->after('refuse_type');
        });
    }
}
