<?php

namespace Akika\MoMo;

use Akika\MoMo\Actions\CreateApiKeyAction;
use Akika\MoMo\Actions\CreateApiUserAction;
use Akika\MoMo\Actions\GetApiUserAction;
use Akika\MoMo\Config\MoMoConfig;
use Akika\MoMo\Products\Disbursement;

class MoMo
{
    public MoMoConfig $moMoConfig;

    public function __construct(
        ?string $secondaryKey = null,
        ?string $userReferenceId = null,
        ?string $apiKey = null,
    ) {
        $this->moMoConfig = new MoMoConfig($secondaryKey, $userReferenceId, $apiKey);
    }

    public function with(
        ?string $secondaryKey = null,
        ?string $userReferenceId = null,
        ?string $apiKey = null,
    ): self {
        return new self(
            $secondaryKey,
            $userReferenceId,
            $apiKey,
        );
    }

    public function createApiUser(): void
    {
        (new CreateApiUserAction($this->moMoConfig))();
    }

    /** @return array<string, string> */
    public function getApiUser(): array
    {
        return (new GetApiUserAction($this->moMoConfig))();
    }

    public function createApiKey(): string
    {
        return (new CreateApiKeyAction($this->moMoConfig))();
    }

    public function disbursement(): Disbursement
    {
        return new Disbursement;
    }
}
