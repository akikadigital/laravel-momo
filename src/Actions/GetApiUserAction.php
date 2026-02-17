<?php

namespace Akika\MoMo\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetApiUserAction
{
    public string $secondaryKey;

    public string $url;

    public function __construct()
    {
        $env = Config::string('momo.env');
        $this->secondaryKey = Config::string("momo.{$env}.secondary_key");
        $xReferenceId = Config::string("momo.{$env}.user_reference_id");

        $baseUrl = Config::string("momo.{$env}.base_url");
        $path = Config::string('momo.url_paths.get_api_user');
        $path = str_replace('{referenceId}', $xReferenceId, $path);
        $this->url = $baseUrl.$path;
    }

    /** @return array<string, string> */
    public function __invoke(): array
    {
        /** @var array<string, string> */
        $response = Http::acceptJson()
            ->withHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey)
            ->get($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
