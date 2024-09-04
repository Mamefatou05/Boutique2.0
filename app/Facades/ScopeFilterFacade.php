<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ScopeFilterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Filter'; 
    }
}