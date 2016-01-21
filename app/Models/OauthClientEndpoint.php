<?php


namespace App\Models;


class OauthClientEndpoint extends ModelBase
{
    protected $table = 'oauth_client_endpoints';
    protected $fillable = ['client_id', 'redirect_uri'];
}