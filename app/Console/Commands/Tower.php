<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Scripts\Tower as cTower;

class Tower extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tower';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Tower messages to terminals.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
