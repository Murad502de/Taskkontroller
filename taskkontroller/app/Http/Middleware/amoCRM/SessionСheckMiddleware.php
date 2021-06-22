<?php

namespace App\Http\Middleware\amoCRM;

use Closure;
use App\Models\AmoSession;

class SessionСheckMiddleware
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
        $amoSession = new AmoSession();
        $subdomain = $request->query( 'subdomain' );

        if ( $amoSession->getSessionState( $subdomain ) )
        {
            return response( [ 'Processing' ], 202 );
        }
        else
        {
            return $next( $request );
        }
    }
}
