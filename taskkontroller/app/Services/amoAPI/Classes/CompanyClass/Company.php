<?php

namespace App\Services\amoAPI\Classes\CompanyClass;

use App\Services\amoAPI\Http\Http as Http;

class Company
{
    private $Http = null;
    private $CompanyDatenbank = null;
    private $accountRequestData = null;

    private $maxNumCompaniesToUpdate = null;

    function __construct( $accountRequestData = null )
    {
        //$this->CompanyDatenbank = new Datenbank();
        $this->accountRequestData = $accountRequestData;

        $this->Http = new Http();

        $this->maxNumCompaniesToUpdate = 200;

        //echo "Company\r\n";
    }

    public function create(){}
    public function delete(){}
    public function update(){}
    public function addToLead(){}
    public function addNote(){}
    public function addTask(){}

    public function getById( $id = null )
    {
        if ( !$id ) return false;

        // Accesstoken prufen und aktualisieren, wenn es benotigt ist 
        //$this->requestData = $this->Http->accessTokenVerification($this->requestData);
        
        //Формируем Httpheaders для запроса
        $this->Http->Httpheaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accountRequestData['access_token']
        ];

        //Формируем URL для запроса
        $this->Http->link = 'https://' . $this->accountRequestData['subdomain'] . '.amocrm.ru/api/v4/companies/' . $id;

        // Serveranfrage ausführen
        return $this->Http->sendRequest( false, 'GET' );
    }

    public function updateN( $updateData = null )
    {
        if ( !$updateData ) return false;

        // Accesstoken prufen und aktualisieren, wenn es benotigt ist 
        //$this->requestData = $this->Http->accessTokenVerification($this->requestData);
            
        //Формируем Httpheaders для запроса
        $this->Http->Httpheaders = [
            'Content-Type: application/json-patch+json',
            'Authorization: Bearer ' . $this->accountRequestData[ 'access_token' ]
        ];

        //Формируем URL для запроса
        $this->Http->link = 'https://' . $this->accountRequestData[ 'subdomain' ] . '.amocrm.ru/api/v4/companies';

        if ( \count( $updateData ) > $this->maxNumCompaniesToUpdate )
        {
            $updateData = \array_chunk( $updateData, $this->maxNumCompaniesToUpdate );

            \file_put_contents( 'data/debug/' . $this->accountRequestData[ 'subdomain' ] . '_Companies_update_limit.txt', \print_r( $updateData, true ) ); /* Debug */

            for ( $updateDataIndex = 0; $updateDataIndex < \count( $updateData ); $updateDataIndex++ )
            {
                \file_put_contents( 'data/debug/' . $this->accountRequestData[ 'subdomain' ] . '_Companies_update_limit_request.txt', \print_r( $updateData[ $updateDataIndex ], true ), FILE_APPEND ); /* Debug */

                // Serveranfrage ausführen
                $this->Http->sendRequest( $updateData[ $updateDataIndex ], 'PATCH' );

                // 0.5 Sekunden warten
                usleep( 500000 );
            }
        }
        else
        {
            \file_put_contents( 'data/debug/' . $this->accountRequestData[ 'subdomain' ] . '_Companies_update.txt', \print_r( $updateData, true ) ); /* Debug */

            // Serveranfrage ausführen
            return $this->Http->sendRequest( $updateData, 'PATCH' );
        }
    }
}