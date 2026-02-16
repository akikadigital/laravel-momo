<?php

namespace Akika\MoMo\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiUserAction
{
    public string $secondaryKey;

    public string $xReferenceId;

    public string $callbackHost;

    public string $url;

    public function __construct()
    {
        $env = Config::string('momo.env');
        $this->secondaryKey = Config::string("momo.{$env}.secondary_key");
        $this->xReferenceId = Config::string("momo.{$env}.user_reference_id");

        $this->callbackHost = Config::string('momo.provider_callback_host');

        $baseUrl = Config::string("momo.{$env}.base_url");
        $path = Config::string('momo.url_paths.create_api_user');
        $this->url = $baseUrl.$path;
    }

    public function __invoke(): void
    {
        Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->secondaryKey,
            'X-Reference-Id' => $this->xReferenceId,
        ])
            ->post($this->url, [
                'providerCallbackHost' => $this->callbackHost,
            ])
            ->throw();
    }
}
