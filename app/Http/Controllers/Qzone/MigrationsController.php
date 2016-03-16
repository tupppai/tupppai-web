<?php namespace App\Http\Controllers\Qzone;

use App\Http\Controllers\Controller;
use App\Jobs\QzoneMigrations;
use Illuminate\Support\Facades\Queue;

class MigrationsController extends Controller
{
	public function show()
	{
		Queue::later(5,new QzoneMigrations());
	}


}
