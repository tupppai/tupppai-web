<?php namespace App\Handles;

use Illuminate\Support\Facades\Event;
use Whoops\Exception\ErrorException;


class Handle
{
     //Default
     const HANDLE_EVENT = 'App\Events\HandleEvent';
     const BACKEND_HANDLE_PATH = 'App\Handles\Backend\\';
     const FRONTEND_HANDLE_PATH = 'App\Handles\Frontend\\';
     //Listen Code
//    const BACKEND_HANDLE_LOVE = 'LoveHandle';
//    const FRONTEND_HANDLE_LOVE = 'LoveHandle';

     public static function Fire($listenCode, array $arguments = [])
     {
          $class = constant('static::HANDLE_EVENT');
          return Event::fire(new $class($listenCode, $arguments));
     }


     public static function Listen($event)
     {
          $ListenClass = static::getListenClass($event->listenCode);
          $Listen = new $ListenClass;
          return $Listen->handle($event);
     }

     public static function getListenClass($ListenCode)
     {
          return static::parse($ListenCode);
     }

     public static function parse($ListenCode)
     {
          $handle = static::getHandle($ListenCode);
          var_dump($handle);
          if (stripos($ListenCode, 'BACKEND_HANDLE') === 0) {
               $class = constant('static::BACKEND_HANDLE_PATH') . $handle;
          } else {
               $class = constant('static::FRONTEND_HANDLE_PATH') . $handle;
          }
          return $class;
     }

     public static function getHandle($ListenCode)
     {
          return config('handle.'.$ListenCode);
     }


}