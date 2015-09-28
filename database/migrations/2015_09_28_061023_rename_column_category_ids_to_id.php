<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnCategoryIdsToId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thread_categories', function( $table ){
            $table->renameColumn('category_ids', 'category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thread_categories', function( $table ){
            $table->renameColumn('category_id', 'category_ids');
        });
    }
}
