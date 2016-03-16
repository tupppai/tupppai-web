<?php namespace App\Jobs;


use App\Services\Qzone\Migrations as sMigrations;
use Log;

class UploadDown extends Job
{


	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$upload_down = new \App\Services\UploadDown();
		$upload_down->uploadsDown();
	}


}
