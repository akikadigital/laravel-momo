<?php

namespace Akika\MoMo\Products;

use Akika\MoMo\Actions\Disbursment\CreateAccessTokenAction;
use Akika\MoMo\Actions\Disbursment\GetAccessTokenAction;
use Akika\MoMo\Actions\Disbursment\TransferAction;
use Akika\MoMo\Enums\Currency;

class Disbursement
{
    /** @return array<string, string|int> */
    public function createAccessToken(): array
    {
        return (new CreateAccessTokenAction)();
    }

    public function getAccessToken(): string
    {
        return (new GetAccessTokenAction)();
    }

    public function transfer(
        float $amount,
        Currency $currency,
        ?string $externalId,
        ?string $payeeMsisdn,
        ?string $payeeMessage,
        ?string $payeeNote,
    ): string {
        return (new TransferAction)(
            $this->getAccessToken(),
            $amount,
            $currency,
            $externalId,
            $payeeMsisdn,
            $payeeMessage,
            $payeeNote,
        );
    }
}
