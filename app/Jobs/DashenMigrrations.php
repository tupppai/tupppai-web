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
        Log::info('test',['Dahshen - users添加完毕']);
		sMigrations::inAsks();
        logger('Dahshen - Asks添加完毕','migrations');
		sMigrations::inReplies();
        logger('Dashen - Reply添加完毕','migrations');
		sMigrations::inComment();
        logger('Dashen - Comments添加完毕','migrations');
        sMigrations::inCount();
        logger('Dashen - inCount添加完毕','migrations');
    }


}
