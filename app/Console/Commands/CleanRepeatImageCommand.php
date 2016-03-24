<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Queue;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use DB;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;

class CleanRepeatImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:repeat-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "清除重复的作品或者图片";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $replies = DB::select(DB::raw('select upload_id, count(1) as count from replies where status > 0 group by upload_id having count > 1'));
        dd($replies);
    }
}
