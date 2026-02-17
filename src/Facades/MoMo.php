<?php

namespace Akika\MoMo\Facades;

use Akika\MoMo\Products\Disbursement;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void createApiUser()
 * @method static array<string, string> getApiUser()
 * @method static string createApiKey()
 * @method static Disbursement disbursement()
 *
 * @see \Akika\MoMo\MoMo
 */
class MoMo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Akika\MoMo\MoMo::class;
    }
}
