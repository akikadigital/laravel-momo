<?php

namespace Akika\MoMo\Products;

use Akika\MoMo\Actions\Disbursment\CreateAccessTokenAction;
use Akika\MoMo\Actions\Disbursment\GetAccessTokenAction;
use Akika\MoMo\Actions\Disbursment\GetTransferStatusAction;
use Akika\MoMo\Actions\Disbursment\TransferAction;
use Akika\MoMo\Config\MoMoConfig;
use Akika\MoMo\Enums\Currency;

class Disbursement
{
    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig) {}

    /** @return array<string, string|int> */
    public function createAccessToken(): array
    {
        return (new CreateAccessTokenAction($this->moMoConfig))();
    }

    public function getAccessToken(): string
    {
        return (new GetAccessTokenAction($this->moMoConfig))();
    }

    public function transfer(
        float $amount,
        Currency $currency,
        ?string $externalId = null,
        ?string $payeeMsisdn = null,
        ?string $payerMessage = null,
        ?string $payeeNote = null,
    ): string {
        return (new TransferAction($this->moMoConfig))(
            $this->getAccessToken(),
            $amount,
            $currency,
            $externalId,
            $payeeMsisdn,
            $payerMessage,
            $payeeNote,
        );
    }

    /** @return array<string, string|array<string, string>> */
    public function getTransferStatus(string $referenceId): array
    {
        return (new GetTransferStatusAction($this->moMoConfig))($this->getAccessToken(), $referenceId);
    }
}
