<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperByToAsgnmnt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_parttime')->table( 'assignments', function( $table ){
            $table->integer( 'oper_by' );
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
            $table->dropColumn( 'oper_by' );
        });
    }
}
