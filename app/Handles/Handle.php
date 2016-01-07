<?php namespace App\Handles;


interface Handle
{
     public static function Fire($listenCode, array $arguments = []);

     public static function Listen($event);
}