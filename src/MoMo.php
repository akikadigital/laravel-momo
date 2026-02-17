<?php

namespace Akika\MoMo;

use Akika\MoMo\Actions\CreateApiKeyAction;
use Akika\MoMo\Actions\CreateApiUserAction;
use Akika\MoMo\Actions\GetApiUserAction;

class MoMo
{
    public function createApiUser(): void
    {
        (new CreateApiUserAction)();
    }

    /** @return array<string, string> */
    public function getApiUser(): array
    {
        return (new GetApiUserAction)();
    }

    public function createApiKey(): string
    {
        return (new CreateApiKeyAction)();
    }
}
