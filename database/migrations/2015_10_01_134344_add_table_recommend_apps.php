<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableRecommendApps extends Migration
{
    public function up()
    {

        Schema::create('recommend_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_name');
            $table->unsignedInteger('logo_upload_id');
            $table->string('jumpurl');
            $table->bigInteger('order_by');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('del_time');
            $table->bigInteger('del_by');
        });
    }

    public function down()
    {
        Schema::drop( 'recommend_apps' );
    }
}
