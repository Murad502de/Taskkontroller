<?php

namespace App\Services\amoAPI\Auth;

use App\Services\amoAPI\Http\Http as Http;
use Illuminate\Http\Request;

class OAuth
{
    // Формируем массив авторизации
    private $oAuthDaten = [
        'client_id' => null,
        'client_secret' => null,
        'grant_type' => 'authorization_code',
        'code' => null,
        'redirect_uri' => null,
        'subdomain' => null
    ];

    private $Http = null;
    private $AuthData = null;

    function __construct( $OAuthParam = null )
    {
        $this->Http = new Http();
    }

    public function auth ( $OAuthParam ) // FIXME
    {
        $oAuthDaten = [];

        //Формируем URL для запроса
        $this->Http->link = 'https://' . $OAuthParam[ 'subdomain' ] . '.amocrm.ru/oauth2/access_token';

        //Формируем Httpheaders для запроса
        $this->Http->Httpheaders = [
            'Content-Type:application/json'
        ];

        $oAuthDaten[ 'client_id' ] = $OAuthParam[ 'client_id' ];
        $oAuthDaten[ 'client_secret' ] = $OAuthParam[ 'secret_code' ];
        $oAuthDaten[ 'code' ] = $OAuthParam[ 'auth_code' ];
        $oAuthDaten[ 'redirect_uri' ] = $OAuthParam[ 'redirect_uri' ];
        $oAuthDaten[ 'subdomain' ] = $OAuthParam[ 'subdomain' ];
        $oAuthDaten[ 'grant_type' ] = 'authorization_code';

        $response = $this->Http->sendRequest( $oAuthDaten );

        if ( !$response[ 'error' ] ) // prüfen, ob error bei der Authorisation passiert ist
        {
            $response[ 'out' ][ 'when_expires' ] = time() + $response[ 'out' ]['expires_in'] - 400;
            $response[ 'out' ][ 'client_id' ] = $oAuthDaten['client_id'];
            $response[ 'out' ][ 'client_secret' ] = $oAuthDaten['client_secret'];
            $response[ 'out' ][ 'redirect_uri' ] = $oAuthDaten['redirect_uri'];
            $response[ 'out' ][ 'subdomain' ] = $oAuthDaten['subdomain'];
        }

        return $response;
    }
}