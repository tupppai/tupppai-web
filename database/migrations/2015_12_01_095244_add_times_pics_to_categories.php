<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimesPicsToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function( $table ){
            $table->integer( 'start_time' );
            $table->integer( 'end_time'   );
            $table->string( 'pc_pic'  );
            $table->string( 'app_pic' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'categories', function( $table ){
            $table->dropColumn( 'start_time' );
            $table->dropColumn( 'end_time'   );
            $table->dropColumn( 'pc_pic'  );
            $table->dropColumn( 'app_pic' );
        });
    }
}
