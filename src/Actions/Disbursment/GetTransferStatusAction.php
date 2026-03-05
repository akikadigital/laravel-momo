<?php

namespace Akika\MoMo\Actions\Disbursment;

use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetTransferStatusAction
{
    public string $path;

    public string $url;

    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig)
    {
        $env = Config::string('momo.env');
        $baseUrl = Config::string("momo.{$env}.base_url");
        $path = Config::string('momo.disbursement.url_paths.get_transfer_status');
        $this->url = $baseUrl.$path;
    }

    /** @return array<string, string|array<string, string>> */
    public function __invoke(
        string $accessToken,
        string $referenceId,
    ): array {
        $this->url = str_replace('{referenceId}', $referenceId, $this->url);

        /** @var array<string, string|array<string, string>> */
        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->moMoConfig->getSecondaryKey(),
                'X-Target-Environment' => $this->moMoConfig->getTargetEnvironment(),
            ])
            ->get($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
