<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class amoAuthKeys extends Model
{
    protected $table;

    function __construct()
    {
        $this->table = 'amo_auth_keys';
    }

    public function getAmoAuthKeys () 
    {
        return $this->all()->first();
    }
}
