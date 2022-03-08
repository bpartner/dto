<?php

namespace Bpartner\Dto\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method build(string $classname, array $data)
 */
class DtoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'dto';
    }
}
