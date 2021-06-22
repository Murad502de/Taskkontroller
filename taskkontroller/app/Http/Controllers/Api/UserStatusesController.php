<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Authorization;

class UserStatusesController extends Controller
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

        return $authorization->getAccountData( $subdomain )->user_statuses;
    }

    public function set( Request $request, Authorization $authorization )
    {
        $subdomain = $request->query( 'subdomain' );
        $fieldName = 'user_statuses';
        $userStatuses = $request->all()[ 'userStatuses' ];

        try
        {
            if ( !$authorization->updateAccountData( $subdomain, $fieldName, $userStatuses ) )
            {
                throw new \Exception( 'Fehler bei der Benutzerstatusdatenaktualisierung', 400 );
            }
        }
        catch ( \Exception $e )
        {
            return response( [ $e->getMessage() ], $e->getCode() );
        }

        return response( [ 'Daten wurden erfolgreich aktualisiert' ], 200 );
    }
}
