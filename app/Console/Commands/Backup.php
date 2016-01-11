<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Queue;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Jobs\Backup as jBackup;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "mysql数据备份";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Queue::push(new jBackup());
    }
}
