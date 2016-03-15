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
		sMigrations::inUsers();
		Log::info('test', ['qzone - users添加完毕']);
		sMigrations::inAsks();
		Log::info('migrations',['qzone - Questions to Asks添加完毕']);
		sMigrations::inReplies();
		Log::info('migrations',['qzone - Reply添加完毕']);
		sMigrations::inComment();
		Log::info('migrations',['qzone - Qcomments to Comments添加完毕']);
		sMigrations::praisesInCount();
		Log::infoger('migrations', ['qzone - praises添加完毕']);
	}


}
