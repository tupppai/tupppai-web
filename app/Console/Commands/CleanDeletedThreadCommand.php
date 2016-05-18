<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Queue;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use DB;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\ThreadCategory as mThreadCategory;
use App\Services\ThreadCategory as sThreadCategory;

class CleanDeletedThreadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:deleted-thread';

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
        $replies = DB::select(DB::raw('select id from replies where status = 0'));
	foreach($replies as $reply) {
		sThreadCategory::deleteThread( 1, mReply::TYPE_REPLY, $reply->id, 0, $reason = 'repeat');
		echo $reply->id . "============\n";
	}
	$asks = DB::select(DB::raw('select id from asks where status = 0'));
	foreach($asks as $ask) {
		sThreadCategory::deleteThread( 1, mAsk::TYPE_ASK, $ask->id, 0, $reason = 'repeat');
		echo $ask->id . "============\n";
	}
    }
}
