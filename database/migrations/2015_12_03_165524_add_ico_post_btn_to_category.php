<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIcoPostBtnToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'categories', function( $table ){
            $table->string('icon')->nullable()->after('description');
            $table->string('post_btn')->nullable()->after('icon');
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
            $table->dropColumn( 'icon' );
            $table->dropColumn( 'post_btn' );
        });
    }
}
