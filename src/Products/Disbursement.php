<?php

namespace Akika\MoMo\Products;

use Akika\MoMo\Actions\Disbursment\CreateAccessTokenAction;

class Disbursement
{
    /** @return array<string, string|int> */
    public function createAccessToken(): array
    {
        return (new CreateAccessTokenAction)();
    }
}
