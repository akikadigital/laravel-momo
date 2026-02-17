<?php

namespace Akika\MoMo\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiKeyAction
{
    public string $secondaryKey;

    public string $url;

    public function __construct()
    {
        $env = Config::string('momo.env');
        $this->secondaryKey = Config::string("momo.{$env}.secondary_key");
        $xReferenceId = Config::string("momo.{$env}.user_reference_id");

        $path = Config::string('momo.url_paths.create_api_key');
        $path = str_replace('{referenceId}', $xReferenceId, $path);

        $baseUrl = Config::string("momo.{$env}.base_url");
        $this->url = $baseUrl.$path;
    }

    public function __invoke(): string
    {
        /** @var string */
        $response = Http::acceptJson()
            ->withHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey)
            ->post($this->url)
            ->throw()
            ->json('apiKey');

        return $response;
    }
}
