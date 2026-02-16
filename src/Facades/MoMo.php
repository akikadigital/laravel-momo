<?php

namespace Akika\MoMo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Akika\MoMo\MoMo
 */
class MoMo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Akika\MoMo\MoMo::class;
    }
}
