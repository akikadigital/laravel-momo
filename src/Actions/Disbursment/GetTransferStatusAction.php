<?php

namespace Akika\MoMo\Actions\Disbursment;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetTransferStatusAction
{
    public string $env;

    public string $secondaryKey;

    public string $path;

    public string $url;

    public function __construct()
    {
        $this->env = Config::string('momo.env');
        $this->secondaryKey = Config::string("momo.{$this->env}.secondary_key");

        $path = Config::string('momo.disbursement.url_paths.get_transfer_status');
        $baseUrl = Config::string("momo.{$this->env}.base_url");
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
                'Ocp-Apim-Subscription-Key' => $this->secondaryKey,
                'X-Target-Environment' => $this->env,
            ])
            ->get($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
