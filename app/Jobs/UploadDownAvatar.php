<?php namespace App\Jobs;


use App\Services\Qzone\Migrations as sMigrations;
use Log;

class UploadDownAvatar extends Job
{


	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$upload_down = new \App\Services\UploadDown();
		$upload_down->uploadUserAvatar();
	}


}
