<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\ActionLog as mActionLog;

class ActionLog extends Job
{
    public $log;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $log )
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $log = new mActionLog;
        foreach($this->log as $key=>$val) {
            $log->$key = $val;
        }
        $log->table = $log->get_table($log->uid);
        $log->create_time = time();

        $log->save();
    }


}
