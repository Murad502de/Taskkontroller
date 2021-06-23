<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Authorization;

class SettingsController extends Controller
{
    function __construct(){}

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Authorization  $authorization
     * @return \Illuminate\Http\Response
    */
    public function get( Request $request, Authorization $authorization )
    {
        $subdomain = $request->query( 'subdomain' );

        $getAccountData = $authorization->getAccountData( $subdomain );

        return $getAccountData ? $getAccountData->settings : response( [ 'No Content' ], 204 );
    }

    public function set( Request $request, Authorization $authorization )
    {
        $subdomain = $request->query( 'subdomain' );
        $fieldName = 'settings';
        $settings = $request->all()[ 'settings' ];

        try
        {
            if ( !$authorization->updateAccountData( $subdomain, $fieldName, $settings ) )
            {
                throw new \Exception( 'Fehler bei der Einstellungsatenaktualisierung', 400 );
            }
        }
        catch ( \Exception $e )
        {
            return response( [ $e->getMessage() ], $e->getCode() );
        }

        return response( [ 'Daten wurden erfolgreich aktualisiert' ], 200 );
    }
}