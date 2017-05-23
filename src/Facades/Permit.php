<?php

namespace Nahid\Permit\Facades;

use Illuminate\Support\Facades\Facade;

class Permit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'permit';
    }
}