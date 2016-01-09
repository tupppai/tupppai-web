<?php 
namespace App\Jobs;

use Mail;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackUp extends Job {

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
    	$filename = data('YmdHis');
    	$db_name  = env('DB_USERNAME');
    	$db_pwd   = env('DB_PASSWORD');

    	//back up the database
    	$backup = new Process("mysqldump mysql -u$db_name -p$db_pwd | gzip > /data/storage/backup/mysql/psgod_$filename.gz");
        $backup->run();
        
        if ($backup->isSuccessful()) {
        	//mail to backup
        	$email = 'billqiang@qq.com';
	        $name  = 'junqiang';

	        $data = ['email'=>$email, 'name'=>$name];

	        Mail::send('mysql backup' , $data, function($message) {
	        	$message->attach("/data/storage/backup/mysql/psgod_$filename.gz");
	        });

	        // $rm_backup = new Process("cd /data/storage/backup/mysql; rm -rf $filename.gz");
	        // $rm_backup->run();
        }

        // executes after the command finishes
        if (!$backup->isSuccessful()) {
            throw new ProcessFailedException($backup);
        }
    }
}