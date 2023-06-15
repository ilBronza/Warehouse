<?php

namespace IlBronza\Warehouse\Facades;

use Illuminate\Support\Facades\Facade;

class Warehouse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'warehouse';
    }
}
