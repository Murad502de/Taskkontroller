<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Services\amoAPI\Auth\OAuth;

class Authorization extends Model
{
    private $amoAuth;

    protected $table;

    function __construct( $tableName = '' )
    {
        $this->table = $tableName;
        $this->amoAuth = new OAuth();
    }

    public function logIn( $redirectData, $amoAuthKeysData, Request $request )
    {
        $OAuthParam = \json_decode( $request->all()[ 'amoDaten' ], true );
        $OAuthParam[ 'auth_code' ] = $redirectData[ 'auth_code' ];
        $OAuthParam[ 'client_id' ] = $amoAuthKeysData[ 'client_id' ];
        $OAuthParam[ 'secret_code' ] = $amoAuthKeysData[ 'secret_code' ];

        $response = $this->amoAuth->auth( $OAuthParam );

        $this->subdomain = $request->query( 'subdomain' );
        $this->code = $response[ 'code' ];

        if ( !$response[ 'error' ] ) // prüfen, ob error bei der Authorisation passiert ist
        {
            // kein Fehler
            $this->table = 'amocrm_accounts';

            $users = \json_decode( $request->all()[ 'amoDaten' ], true )[ 'users' ];
            $userStatuses = [];

            foreach ( $users as $key => $value )
            {
                $userStatuses[ $key ] = [
                    'name' => $value,
                    'status' => false
                ];
            }

            $settings = [
                'tasks' => [
                    'value' => false
                ],
    
                'contacts' => [
                    'value' => false,
    
                    'tasks' => [
                        'value' => false
                    ],
    
                    'companies' => [
                        'value' => false,
        
                        'tasks' => [
                            'value' => false
                        ],
                    ]
                ],
    
                'companies' => [
                    'value' => false,
    
                    'tasks' => [
                        'value' => false
                    ],
                ]
            ];

            $this->access_token = $response[ 'out' ][ 'access_token' ];
            $this->client_id = $response[ 'out' ][ 'client_id' ];
            $this->client_secret = $response[ 'out' ][ 'client_secret' ];
            $this->refresh_token = $response[ 'out' ][ 'refresh_token' ];
            $this->redirect_uri = $response[ 'out' ][ 'redirect_uri' ];
            $this->when_expires = $response[ 'out' ][ 'when_expires' ];
            $this->user_statuses = \json_encode( $userStatuses );
            $this->settings = \json_encode( $settings );
        }
        else
        {
            // ein Fehler
            $this->table = 'amocrm_errors';

            $this->out = \json_encode( $response[ 'out' ] );
            $this->exportData = \json_encode( $response[ 'exportData' ] );
            $this->link = $response[ 'link' ];
            $this->headers = \json_encode( $response[ 'headers' ] );
        }

        $this->save();

        return $response;

        //return response( [ 'OK', $OAuthParam ], 200 );
    }
    
    public function logOut( Request $request )
    {
        // TODO den Abmeldungsprozess implementieren
        $this->table = 'amocrm_accounts';
        $subdomain = $request->query( 'subdomain' );
        
        return $this->where( 'subdomain', '=', $subdomain )->delete();
    }

    public function getAccountData( $subdomain )
    {
        $this->table = 'amocrm_accounts';

        return $this->where( 'subdomain', '=', $subdomain )->first();
    }

    public function updateAccountData( $subdomain, $fieldName, $updateData )
    {
        $this->table = 'amocrm_accounts';

        return $this->where( 'subdomain', '=', $subdomain )->update( [ $fieldName => $updateData ] );
    }
}
