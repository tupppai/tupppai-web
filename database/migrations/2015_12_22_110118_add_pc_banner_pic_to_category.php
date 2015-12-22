<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPcBannerPicToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'categories', function( $table ){
            $table->string( 'pc_banner_pic', 255 )->after('banner_pic');
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
            $table->dropColumn( 'pc_banner_pic' );
        });
    }
}
