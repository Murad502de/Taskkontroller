<?php

namespace App\Http\Middleware\amoCRM;

use Closure;
use App\Models\amoCRMredirect;

class amoAuthCodeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        $action = $request->query( 'action' );
        
        $amoRedirect = new amoCRMredirect();

        $redirectData = $amoRedirect->getRedirectData( $request->query( 'subdomain' ) );

        if ( $redirectData )
        {
            $expirationDate = $redirectData->when_expires;

            if ( time() >= $expirationDate )
            {
                return response( [ 'Request Timeout' ], 408 );
            }
            else
            {
                return $next( $request );
            }
        }
        else
        {
            return response( [ 'Not Found' ], 404 );
        }
    }
}
