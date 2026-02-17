<?php

namespace Akika\MoMo\Actions\Disbursment;

use Akika\MoMo\Enums\Currency;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TransferAction
{
    public string $env;

    public string $secondaryKey;

    public string $callbackUrl;

    public string $url;

    public function __construct()
    {
        $this->env = Config::string('momo.env');
        $this->secondaryKey = Config::string("momo.{$this->env}.secondary_key");

        $this->callbackUrl = Config::string('momo.disbursement.callback_url');

        $baseUrl = Config::string("momo.{$this->env}.base_url");
        $path = Config::string('momo.disbursement.url_paths.transfer');
        $this->url = $baseUrl.$path;
    }

    public function __invoke(
        string $accessToken,
        float $amount,
        Currency $currency,
        ?string $externalId = null,
        ?string $payeeMsisdn = null,
        ?string $payerMessage = null,
        ?string $payeeNote = null,
    ): string {
        $referenceId = Str::uuid()->toString();

        $body = array_filter([
            'amount' => number_format($amount, 2, thousands_separator: ''),
            'currency' => $currency->value,
            'externalId' => $externalId,
            'payee' => $payeeMsisdn
                ? ['partyIdType' => 'MSISDN', 'partyId' => $payeeMsisdn]
                : null,
            'payerMessage' => $payerMessage,
            'payeeNote' => $payeeNote,
        ]);

        Http::acceptJson()
            ->withToken($accessToken)
            ->withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->secondaryKey,
                'X-Callback-Url' => $this->callbackUrl,
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => $this->env,
            ])
            ->post($this->url, $body)
            ->throw();

        return $referenceId;
    }
}
