<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Scripts\Tower as cTower;

class Helper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "1. 时间小帮手 \n";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->ask("What do u want:
            1. date to timestamp helper
            2. timestamp to date helper");
        switch($type) {
        case 1:
            $date = $this->ask('Input date: ');
            echo strtotime($date);
            break;
        case 2:
            $time = $this->ask('Input time: ');
            echo date('Ymd H:i:s', $time);
            break;
        }
        dd("");
    }
}
