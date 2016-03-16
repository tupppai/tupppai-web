<?php namespace App\Http\Controllers\Dashen;

use App\Http\Controllers\Controller;
use App\Jobs\DashenMigrations;
use Illuminate\Support\Facades\Queue;

class MigrationsController extends Controller
{
	public function show()
	{
		Queue::later(5,new DashenMigrations());
	}


}
