<?php namespace App\Trades;

use Illuminate\Support\Facades\Event;

class Trade
{
     const HANDLE_EVENT         = 'App\Events\HandleEvent';
     const BACKEND_HANDLE_PATH  = 'App\Handles\Backend\\';
     const FRONTEND_HANDLE_PATH = 'App\Handles\Frontend\\';

     /**
      * 发送事件
      */
     public static function Fire($listenCode, array $arguments = [])
     {
          $class = self::HANDLE_EVENT;
          return Event::fire(new $class($listenCode, $arguments));
     }

     /**
      * 监听事件
      */
     public static function Listen($event)
     {
          $ListenClass = static::getListenClass($event->listenCode);
          $Listen = new $ListenClass;
          return $Listen->handle($event);
     }

     /**
      * 私有函数-解析handler class
      */
     private static function getListenClass($ListenCode)
     {
          return static::parse($ListenCode);
     }

     private static function parse($ListenCode)
     {
          $handle = static::getHandle($ListenCode);
          if (stripos($ListenCode, 'BACKEND_HANDLE') === 0) {
               $class = self::BACKEND_HANDLE_PATH . $handle;
          } else {
               $class = self::FRONTEND_HANDLE_PATH . $handle;
          }
          return $class;
     }

     private static function getHandle($ListenCode)
     {
          return config('handle.'.$ListenCode);
     }

}
