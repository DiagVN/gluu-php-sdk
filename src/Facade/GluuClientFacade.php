<?php
namespace Gluu\Facade;

use Illuminate\Support\Facades\Facade;

class GluuClientFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GluuClient';
    }
}