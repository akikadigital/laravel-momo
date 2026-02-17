<?php

namespace Akika\MoMo\Actions\Disbursment;

use Akika\MoMo\Config\MoMoConfig;

class GetAccessTokenAction
{
    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig) {}

    public function __invoke(): string
    {
        $tokenResponse = (new CreateAccessTokenAction($this->moMoConfig))();

        /** @var string */
        $accessToken = $tokenResponse['access_token'];

        return $accessToken;
    }
}
