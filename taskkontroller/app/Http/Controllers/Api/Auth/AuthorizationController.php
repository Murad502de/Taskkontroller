<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\Authorization;
use App\Models\amoCRMredirect;
use App\Models\amoAuthKeys;

class AuthorizationController extends Controller
{

    function __construct()
    {
        // amoAuthCode prüfen
        $this->middleware( 'amoAuthCodeCheck' )->except( 'logout' );;
    }

    /**
     * OAuth 2.0 authorization in amoCRM
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Authorization  $authorization
     * @return \Illuminate\Http\Response
    */

    public function login( Request $request, Authorization $authorization )
    {
        $amoRedirect = new amoCRMredirect();
        $amoAuthKeys = new amoAuthKeys();

        $redirectDataAll = $amoRedirect->getRedirectData( $request->query( 'subdomain' ) );
        $amoAuthKeysDataAll = $amoAuthKeys->getAmoAuthKeys();

        $redirectData = [
            'auth_code' => $redirectDataAll->auth_code
        ];

        $amoAuthKeysData = [
            'client_id' => $amoAuthKeysDataAll->client_id,
            'secret_code' => $amoAuthKeysDataAll->client_secret,
        ];

        $amoRedirect->deleteRedirectData( $request->query( 'subdomain' ) );

        return $authorization->logIn( $redirectData, $amoAuthKeysData, $request )[ 'error' ] ? response( [ 'Bad Request' ], 400 ) : response( [ 'OK' ], 200 );
    }

    public function logout( Request $request, Authorization $authorization )
    {
        return $authorization->logOut( $request ) ? response( [ 'OK' ], 200 ) : response( [ 'Bad Request' ], 400 );
    }
}