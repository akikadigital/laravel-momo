<?php

namespace Akika\LaravelMomo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Akika\LaravelMomo\LaravelMomo
 */
class LaravelMomo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Akika\LaravelMomo\LaravelMomo::class;
    }
}
