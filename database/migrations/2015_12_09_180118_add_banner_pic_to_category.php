<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBannerPicToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'categories', function( $table ){
            $table->string( 'banner_pic', 255 )->after('app_pic');
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
            $table->dropColumn( 'banner_pic' );
        });
    }
}
