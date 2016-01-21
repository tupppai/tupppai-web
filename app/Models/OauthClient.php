<?php


namespace App\Models;


class OauthClient extends ModelBase
{
    protected $table = 'oauth_clients';
    protected $fillable = ['id', 'secret', 'name'];
}