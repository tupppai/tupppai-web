<?php
/**
 * Created by PhpStorm.
 * User: zhiyong
 * Date: 16/1/12
 * Time: 上午11:19
 */

namespace App\Handles\Backend;

use App\Events\Event;
use App\Services\User;

class AddWaistcoatUserHandle
{
    public function handle(Event $event)
    {
        list( $username, $password, $nickname, $sex, $phone, $avatar, $role_id ) = $event->arguments;
        User::addWaistcoatUser( $username, $password, $nickname, $sex, $phone, $avatar, $role_id );
    }
}