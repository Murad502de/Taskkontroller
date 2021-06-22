<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AmoSession extends Model
{
    protected $table;

    function __construct()
    {
        $this->table = 'amo_sessions';
    }

    public function startSession( $subdomain )
    {
        $this->subdomain = $subdomain;
        return $this->save();
    }

    public function endSession( $subdomain )
    {
        return $this->where( 'subdomain', '=', $subdomain )->delete();
    }

    public function getSessionState( $subdomain )
    {
        return $this->where( 'subdomain', '=', $subdomain )->first();
    }
}
