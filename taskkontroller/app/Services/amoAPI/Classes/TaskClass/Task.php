<?php

namespace App\Services\amoAPI\Classes\TaskClass;

use App\Services\amoAPI\Http\Http as Http;

class Task
{
    private $Http = null;
    private $TaskDatenbank = null;
    private $accountRequestData = null;

    private $maxNumTasksToUpdate = null;

    function __construct( $accountRequestData = null )
    {
        //$this->TaskDatenbank = new Datenbank();
        $this->accountRequestData = $accountRequestData;

        $this->Http = new Http();

        $this->maxNumTasksToUpdate = 100;

        //echo "Task\r\n";
    }

    public function updateN( $updateData = null )
    {
        if ( !$updateData ) return false;

        // Accesstoken prufen und aktualisieren, wenn es benotigt ist 
        //$this->requestData = $this->Http->accessTokenVerification($this->requestData);
            
        //Формируем Httpheaders для запроса
        $this->Http->Httpheaders = [
            'Content-Type: application/json-patch+json',
            'Authorization: Bearer ' . $this->accountRequestData['access_token']
        ];

        //Формируем URL для запроса
        $this->Http->link = 'https://' . $this->accountRequestData[ 'subdomain' ] . '.amocrm.ru/api/v4/tasks';

        if ( \count( $updateData ) > $this->maxNumTasksToUpdate )
        {
            $updateData = \array_chunk( $updateData, $this->maxNumTasksToUpdate );

            \file_put_contents( 'data/debug/' . $this->accountRequestData[ 'subdomain' ] . '_Tasks_update_limit.txt', \print_r( $updateData, true ) ); /* Debug */

            for ( $updateDataIndex = 0; $updateDataIndex < \count( $updateData ); $updateDataIndex++ )
            {
                \file_put_contents( 'data/debug/' . $this->accountRequestData[ 'subdomain' ] . '_Tasks_update_limit_request.txt', \print_r( $updateData[ $updateDataIndex ], true ), FILE_APPEND ); /* Debug */

                // Serveranfrage ausführen
                $this->Http->sendRequest( $updateData[ $updateDataIndex ], 'PATCH' );

                // 0.5 Sekunden warten
                usleep( 500000 );
            }
        }
        else
        {
            \file_put_contents( 'data/debug/' . $this->accountRequestData[ 'subdomain' ] . '_Tasks_update.txt', \print_r( $updateData, true ) ); /* Debug */

            // Serveranfrage ausführen
            return $this->Http->sendRequest( $updateData, 'PATCH' );
        }
    }

    public function getByQuery( $query = null )
    {
        if ( !$query ) return false;

        // Accesstoken prufen und aktualisieren, wenn es benotigt ist 
        //$this->requestData = $this->Http->accessTokenVerification( $this->requestData );
        
        //Формируем Httpheaders для запроса
        $this->Http->Httpheaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accountRequestData[ 'access_token' ]
        ];

        //Формируем URL для запроса
        $this->Http->link = 'https://' . $this->accountRequestData[ 'subdomain' ] . '.amocrm.ru/api/v4/tasks?' . $query;

        // Serveranfrage ausführen
        return $this->Http->sendRequest( false, 'GET' );
    }
}