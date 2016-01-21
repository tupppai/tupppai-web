<?php namespace App\Handles;

use Illuminate\Support\Facades\Event;
use Whoops\Exception\ErrorException;


class Handle
{
    const HANDLE_SYNC_EVENT    = 'App\Events\HandleSyncEvent';
    const HANDLE_QUEUE_EVENT   = 'App\Events\HandleQueueEvent';

    const BACKEND_HANDLE_PATH  = 'App\Handles\Backend\\';
    const FRONTEND_HANDLE_PATH = 'App\Handles\Frontend\\';
    const TRADE_HANDLE_PATH    = 'App\Handles\Trade\\';
    const API_HANDLE_PATH      = 'App\Handles\Api\\';

    /**
     * 发送事件
     */
    public static function fire($listenCode, array $arguments = [])
    {
        if (isset($arguments['driver']) && $arguments['driver'] == 'sync') {
            $class = self::HANDLE_SYNC_EVENT;
        }
        else {
            $class = self::HANDLE_QUEUE_EVENT;
        }
        return Event::fire(new $class($listenCode, $arguments));
    }

    /**
     * 监听事件
     */
    public static function listen($event)
    {
        $listenCode = $event->listenCode;

        //从config文件中获取类名
        $handle     = config('code.'.$listenCode);
        if (!$handle) {
            return self::handle($event);
        }

        //判断目录层级&归属
        if (stripos($listenCode, 'FRONTEND_HANDLE') === 0) {
            $class = self::FRONTEND_HANDLE_PATH . $handle;
        }
        else if (stripos($listenCode, 'BACKEND_HANDLE') === 0) {
            $class = self::BACKEND_HANDLE_PATH . $handle;
        }
        else if (stripos($listenCode, 'TRADE_HANDLE') === 0) {
            $class = self::TRADE_HANDLE_PATH . $handle;
        }
        else if (stripos($listenCode, 'API_HANDLE') === 0) {
            $class = self::API_HANDLE_PATH . $handle;
        }
        else {
            return self::handle($event);
        }
 
        if (class_exists($class)) {
            return (new $class)->handle($event);
        }

        return self::handle($event);
    }

    public static function handle($event) {
        echo '获取handle文件失败';
        dd($event);
    }
}
