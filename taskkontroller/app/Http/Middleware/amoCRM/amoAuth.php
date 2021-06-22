<?php

namespace App\Http\Middleware\amoCRM;

use Closure;
use App\Models\Authorization;

class amoAuth
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
        $subdomain = $request->query( 'subdomain' );
        $authorization = new Authorization();

        $accountData = $authorization->getAccountData( $subdomain );

        if ( $accountData )
        {
            //echo $accountData->subdomain . '<br>';

            return $next( $request );
        }
        else
        {
            return response( [ 'Account ist nicht angemeldet' ], 401 );
        }
        //echo $subdomain . ' amoAuth Middleware<br>';
    }
}
