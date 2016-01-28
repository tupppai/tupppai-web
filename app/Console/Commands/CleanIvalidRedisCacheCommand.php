<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class CleanIvalidRedisCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:invalid-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理无效用户登陆态缓存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = Redis::keys('laravel:*');
        $invalidCount = 0;
        $cacheCount = count($users);

        $this->output->progressStart($cacheCount);
        foreach ($users as $key){
            $user = Redis::get($key);
            if(!stripos($user,'"uid"')) {
                $invalidCount++;
                Redis::del($key);
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        $this->info(" laravel:key \n 总数{$cacheCount}条数据 \n 无效数据{$invalidCount} \n 处理结果:删除无效数据{$invalidCount}条 ");
        exit(0);

    }
}
