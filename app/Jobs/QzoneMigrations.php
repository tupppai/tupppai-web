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
//		sMigrations::inUsers();
//		Log::info('test', ['qzone - users添加完毕']);
//		sMigrations::inAsks();
//		logger('migrations',['qzone - Questions to Asks添加完毕']);
//		sMigrations::inReplies();
//		logger('migrations',['qzone - Reply添加完毕']);
//		sMigrations::inComment();
//		logger('migrations',['qzone - Qcomments to Comments添加完毕']);
		sMigrations::praisesInCount();
		logger('migrations',['qzone - praises添加完毕']);
	}


}
