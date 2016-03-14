<?php namespace App\Jobs;


use App\Services\Dashen\Migrations as sMigrations;
use Log;
class DashenMigrations extends Job
{


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		sMigrations::inUsers();
        Log::info('migrations',['Dahshen - users添加完毕']);
		sMigrations::inAsks();
        logger('migrations',['Dahshen - Asks添加完毕']);
		sMigrations::inReplies();
        logger('migrations',['Dashen - Reply添加完毕']);
		sMigrations::inComment();
        logger('migrations',['Dashen - Comments添加完毕']);
        sMigrations::inCount();
        logger('migrations',['Dashen - inCount添加完毕']);
    }


}
