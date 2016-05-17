<?php

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class InvitationIndex extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('invitations', function( $table ){
                //todo in好像会中断,需要优化下
                $table->index(['ask_id','invite_uid']);
                $table->index(['invite_uid','status']);
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('invitations', function( $table ){
                $table->dropIndex(['ask_id','invite_uid']);
                $table->dropIndex(['invite_uid','status']);
            });
        }
    }
