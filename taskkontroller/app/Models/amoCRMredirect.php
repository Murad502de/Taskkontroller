<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class amoCRMredirect extends Model
{
    protected $table;

    function __construct()
    {
        $this->table = 'amo_redirects';
    }

    public function saveRedirectData ( $redirectData )
    {
        if ( !$redirectData )
        {
            return response( [ 'No Content ' ], 204 );
        }
        else
        {
            //\file_put_contents( 'data/debug/redirectData.txt', \print_r( $redirectData, true ) );

            $subdomain = explode( '.', $redirectData[ 'referer' ] )[ 0 ];

            $this->where( 'subdomain', '=', $subdomain )->delete();

            $this->subdomain = $subdomain;
            $this->client_id = $redirectData[ 'client_id' ];
            $this->auth_code = $redirectData[ 'code' ];
            $this->when_expires = time() + 1200;

            return $this->save() ? response( [ 'OK' ], 200 ) : response( [ 'Conflict' ], 409 );
        }
    }

    public function getRedirectData ( $subdomain ) 
    {
        return $this->where( 'subdomain', '=', $subdomain )->first();
    }

    public function deleteRedirectData ( $subdomain ) 
    {
        return $this->where( 'subdomain', '=', $subdomain )->delete();
    }
}
