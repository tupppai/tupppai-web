<?php namespace App\Http\Controllers\Admin;

use App\Services\Ask as sAsk;

class TaskController extends ControllerBase
{
    public function indexAction()
    {
        $queue = sAsk::waitingQueue();
        // dd($queue);
        echo count($queue);
    }
}
