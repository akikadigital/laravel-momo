<?php

namespace Akika\MoMo\Actions\Disbursment;

class GetAccessTokenAction
{
    public function __invoke(): string
    {
        $tokenResponse = (new CreateAccessTokenAction)();

        /** @var string */
        $accessToken = $tokenResponse['access_token'];

        return $accessToken;
    }
}
