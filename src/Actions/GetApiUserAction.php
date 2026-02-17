<?php

namespace Akika\MoMo\Actions;

use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetApiUserAction
{
    public string $url;

    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig)
    {
        $path = Config::string('momo.url_paths.get_api_user');
        $path = str_replace('{referenceId}', $this->moMoConfig->getUserReferenceId(), $path);

        $env = Config::string('momo.env');
        $baseUrl = Config::string("momo.{$env}.base_url");
        $this->url = $baseUrl.$path;
    }

    /** @return array<string, string> */
    public function __invoke(): array
    {
        /** @var array<string, string> */
        $response = Http::acceptJson()
            ->withHeader('Ocp-Apim-Subscription-Key', $this->moMoConfig->getSecondaryKey())
            ->get($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
