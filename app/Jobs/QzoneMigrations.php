<?php namespace App\Jobs;


use App\Services\Qzone\Migrations as sMigrations;
use Log;
class QzoneMigrations extends Job
{


	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		//sMigrations::inUsers();
		 Log::info('test',['qzone - users添加完毕']);
        sMigrations::inAsks();
        logger('qzone - Questions to Asks添加完毕','migrations');
        sMigrations::inReplies();
        logger('qzone - Reply添加完毕','migrations');
        sMigrations::inComment();
        logger('qzone - Qcomments to Comments添加完毕','migrations');
        sMigrations::praisesInCount();
        logger('qzone - praises添加完毕','migrations');
	}


}
