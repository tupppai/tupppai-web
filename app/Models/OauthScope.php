<?php


namespace App\Models;


class OauthScope extends ModelBase
{
    protected $table = 'oauth_scopes';
    protected $fillable = ['id', 'description'];
}