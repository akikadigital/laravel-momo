<?php

namespace Akika\MoMo;

use Akika\MoMo\Actions\CreateApiUserAction;

class MoMo
{
    public function createApiUser(): void
    {
        (new CreateApiUserAction)();
    }
}
