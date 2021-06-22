<?php

namespace App\Http\Middleware\amoCRM;

use Closure;
use App\Models\Authorization;
use App\Services\amoAPI\Http\Http as Http;

class amoAccessTokenVerification
{
    private $Http = null;

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
        $authorization = new Authorization( 'amocrm_accounts' );

        $accountData = $authorization->getAccountData( $subdomain );

        if ( time() >= (int)$accountData->when_expires )
        {
            $accessTokenUpdateDaten = [
                'subdomain' => $accountData->subdomain,
                'client_id' => $accountData->client_id,
                'client_secret' => $accountData->client_secret,
                'refresh_token' => $accountData->refresh_token,
                'redirect_uri' => $accountData->redirect_uri,
            ];

            $this->Http = new Http();

            $response = $this->Http->accessTokenUpdate( $accessTokenUpdateDaten );

            if ( !$response[ 'error' ] ) // prüfen, ob error bei der Zugangstokenaktualisierung passiert ist
            {
                // kein Fehler

                echo 'kein Fehler<br>';

                $accountData->access_token = $response[ 'out' ][ 'access_token' ];
                $accountData->refresh_token = $response[ 'out' ][ 'refresh_token' ];
                $accountData->when_expires = time() + $response[ 'out' ]['expires_in'] - 400;
                
                $accountData->save();

                return $next( $request );
            }
            else
            {
                // ein Fehler

                echo 'ein Fehler<br>';

                $tokenUpdateError = new Authorization( 'amocrm_errors' );

                $tokenUpdateError->subdomain = $accountData->subdomain;
                $tokenUpdateError->code = $response[ 'code' ];
                $tokenUpdateError->out = \json_encode( $response[ 'out' ] );
                $tokenUpdateError->exportData = \json_encode( $response[ 'exportData' ] );
                $tokenUpdateError->link = $response[ 'link' ];
                $tokenUpdateError->headers = \json_encode( $response[ 'headers' ] );

                $tokenUpdateError->save();

                return response( [ 'Zugangstoken ist abgelaufen' ], 401 );
            }
        }
        else
        {
            //echo $accountData->when_expires . '<br>';

            return $next( $request );
        }
    }
}
