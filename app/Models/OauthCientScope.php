<?php


namespace App\Models;


class OauthClientScope extends ModelBase
{
    protected $table = 'oauth_client_scopes';
    protected $fillable = ['client_id', 'scope_id'];
}