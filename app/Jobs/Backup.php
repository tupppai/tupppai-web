<?php 
namespace App\Jobs;

use Queue;
use Mail;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Backup extends Job {

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $filename = date('YmdHis');
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

            Mail::send('test' , $data, function($message) use ($filename, $data) {
                $message->to($data['email'], $data['name'])
                    ->subject("数据库文件备份$filename")
                    ->attach("/data/storage/backup/mysql/psgod_$filename.gz");
            });

            //$rm_backup = new Process("cd /data/storage/backup/mysql; rm -rf $filename.gz");
            //$rm_backup->run();
        }

        // executes after the command finishes
        if (!$backup->isSuccessful()) {
            throw new ProcessFailedException($backup);
        }

        //延迟到第二天的凌晨
        $delay = strtotime(date('Ymd 02:00:00') + '+ 1 day') - time();
        Queue::later( $delay, new self());
    }
}
