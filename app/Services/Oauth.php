<?php namespace App\Services;


use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class Account extends ServiceBase
{
    public function get_authorizer()
    {
        $authParams = Authorizer::getAuthCodeRequestParams();

        $formParams = array_except($authParams,'client');

        $formParams['client_id'] = $authParams['client']->getId();

        $formParams['scope'] = \implode(config('oauth2.scope_delimiter'), \array_map(function ($scope) {
            return $scope->getId();
        }, $authParams['scopes']));

        return view('oauth.authorization-form', ['params' => $formParams, 'client' => $authParams['client']]);
    }

    public function post_authorizer()
    {
        $params = Authorizer::getAuthCodeRequestParams();
        $params['user_id'] = 1;
        $redirectUri = '/';

        // If the user has allowed the client to access its data, redirect back to the client with an auth code.
        if (Request::has('approve')) {
            $redirectUri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
        }

        // If the user has denied the client to access its data, redirect back to the client with an error message.
        if (Request::has('deny')) {
            $redirectUri = Authorizer::authCodeRequestDeniedRedirectUri();
        }

        return redirect($redirectUri);
    }
}
