<?php

namespace Akika\MoMo\Actions;

use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiKeyAction
{
    public string $url;

    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig)
    {
        $path = Config::string('momo.url_paths.create_api_key');
        $path = str_replace('{referenceId}', $moMoConfig->getUserReferenceId(), $path);

        $env = Config::string('momo.env');
        $baseUrl = Config::string("momo.{$env}.base_url");
        $this->url = $baseUrl.$path;
    }

    public function __invoke(): string
    {
        /** @var string */
        $response = Http::acceptJson()
            ->withHeader('Ocp-Apim-Subscription-Key', $this->moMoConfig->getSecondaryKey())
            ->post($this->url)
            ->throw()
            ->json('apiKey');

        return $response;
    }
}
