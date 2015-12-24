<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Log;

class BuildApk extends Job 
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('processing apk');

        $androidPath    = '/var/www/tupppai-android';
        $webPath        = '/var/www/ps';

        $process = new Process("cd $androidPath; git pull origin master > /tmp/buildApk.log; ./gradlew assembleTupppaiRelease -Pandroid.injected.signing.store.file=/home/jq/.gradle/keystore -Pandroid.injected.signing.store.password=psgod1234 -Pandroid.injected.signing.key.alias=psgod -Pandroid.injected.signing.key.password=psgod1234 >> /tmp/buildApk.log; cp $androidPath/appStartActivity/build/outputs/apk/tupppai.apk $webPath/public/mobile/apk/tupai.apk");
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
