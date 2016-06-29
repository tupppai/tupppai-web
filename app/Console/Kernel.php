<?php namespace App\Console;

use App\Services\UserRole as sUserRole;
use App\Models\UserRole as mUserRole;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Cache;

use App\Services\Parttime\Task as sTask;

class Kernel extends ConsoleKernel
{

    const RECOMMEND_USER_AMOUNT_PER_DAY = 3;
    const SECONDS_PER_DAY = 1440; //===60/*minutes*/*24/*hours*/;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\Helper::class,
        \App\Console\Commands\Backup::class,
        \App\Console\Commands\Mailer::class,
        \App\Console\Commands\CleanIvalidRedisCacheCommand::class,
        \App\Console\Commands\CleanRepeatImageCommand::class,
        \App\Console\Commands\CleanDeletedThreadCommand::class,
        \App\Console\Commands\CleanBeanstalkdQueueCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
        $schedule->command('inspire')
                 ->hourly();

        // 每天推荐3名用户到首页
        $today_key = config('redis_keys.today_recommend_users');
        $yesterday_key = config('redis_keys.yesterday_recommended_users');

        //get new recommended user
        $schedule->call(function () use( $today_key, $yesterday_key ) {
            $reced_user = Cache::get( $yesterday_key );
            if( !$reced_user ){
                $reced_user = [];
            }

            $all_rec_user_uids = sUserRole::getUidsByIds(mUserRole::ROLE_STAR);
            $available_uids = array_diff($all_rec_user_uids, $reced_user);
            $recommend_uids_key = array_rand( $available_uids, $this::RECOMMEND_USER_AMOUNT_PER_DAY );
            $new_user_ids = [];
            foreach( $recommend_uids_key as $key ){
                $new_user_ids[] = $available_uids[$key];
            }
            Cache::put( $today_key, $new_user_ids, $this::SECONDS_PER_DAY );
        })->dailyAt('00:00'); // = daily() at midnight

        //save today recommended uids
        $schedule->call(function() use ( $today_key, $yesterday_key ){
            $today_uids = Cache::get( $today_key );
            if( !$today_uids ){
                $today_uids = [];
            }
            Cache::put( $yesterday_key, $today_uids, $this::SECONDS_PER_DAY );
        })->dailyAt('12:00'); // at noon
        // End 每天推荐3名用户

        $schedule->call(function(){
            sTask::assign();
        })->daily();
    }
}
