<?php

namespace Akika\MoMo\Actions;

use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiUserAction
{
    public string $callbackHost;

    public string $url;

    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig)
    {
        $this->callbackHost = Config::string('momo.provider_callback_host');

        $env = Config::string('momo.env');
        $baseUrl = Config::string("momo.{$env}.base_url");
        $path = Config::string('momo.url_paths.create_api_user');
        $this->url = $baseUrl.$path;
    }

    public function __invoke(): void
    {
        Http::acceptJson()
            ->withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->moMoConfig->getSecondaryKey(),
                'X-Reference-Id' => $this->moMoConfig->getUserReferenceId(),
            ])
            ->post($this->url, [
                'providerCallbackHost' => $this->callbackHost,
            ])
            ->throw();
    }
}
