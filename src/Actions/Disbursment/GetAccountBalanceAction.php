<?php

namespace Akika\MoMo\Actions\Disbursment;

use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetAccountBalanceAction
{
    public string $env;

    public string $path;

    public string $url;

    public function __construct(public MoMoConfig $moMoConfig = new MoMoConfig)
    {
        $this->env = Config::string('momo.env');
        $baseUrl = Config::string("momo.{$this->env}.base_url");
        $path = Config::string('momo.disbursement.url_paths.get_account_balance');
        $this->url = $baseUrl.$path;
    }

    /** @return array<string, string> */
    public function __invoke(
        string $accessToken,
    ): array {
        /** @var array<string, string> */
        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->moMoConfig->getSecondaryKey(),
                'X-Target-Environment' => $this->env,
            ])
            ->get($this->url)
            ->throw()
            ->json();

        return $response;
    }
}
