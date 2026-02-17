<?php

namespace Akika\MoMo\Actions\Disbursment;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateAccessTokenAction
{
    public string $secondaryKey;

    public string $xReferenceId;

    public string $url;

    public function __construct()
    {
        $env = Config::string('momo.env');
        $this->secondaryKey = Config::string("momo.{$env}.secondary_key");
        $this->xReferenceId = Config::string("momo.{$env}.user_reference_id");

        $baseUrl = Config::string("momo.{$env}.base_url");
        $path = Config::string('momo.disbursement.url_paths.create_access_token');
        $this->url = $baseUrl.$path;
    }

    /** @return array<string, string|int> */
    public function __invoke(): array
    {
        /** @var array<string, string|int> */
        $response = Http::acceptJson()
            ->withBasicAuth($this->xReferenceId, $this->secondaryKey)
            ->withHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey)
            ->post($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
