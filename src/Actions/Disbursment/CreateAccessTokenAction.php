<?php

namespace Akika\MoMo\Actions\Disbursment;

use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateAccessTokenAction
{
    public string $url;

    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig)
    {
        $env = Config::string('momo.env');
        $baseUrl = Config::string("momo.{$env}.base_url");
        $path = Config::string('momo.disbursement.url_paths.create_access_token');
        $this->url = $baseUrl.$path;
    }

    /** @return array<string, string|int> */
    public function __invoke(): array
    {
        /** @var array<string, string|int> */
        $response = Http::acceptJson()
            ->withBasicAuth($this->moMoConfig->getUserReferenceId(), $this->moMoConfig->getApiKey())
            ->withHeader('Ocp-Apim-Subscription-Key', $this->moMoConfig->getSecondaryKey())
            ->post($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
