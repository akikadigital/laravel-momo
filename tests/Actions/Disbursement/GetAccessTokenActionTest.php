<?php

namespace Akika\MoMo\Tests\Actions\Disbursement;

use Akika\MoMo\Actions\Disbursment\GetAccessTokenAction;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetAccessTokenActionTest extends TestCase
{
    public string $secondaryKey;

    public string $xReferenceId;

    public string $apiKey;

    public string $callbackHost;

    public string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set("momo.{$env}.api_key", $this->apiKey = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.disbursement.url_paths.create_access_token');
        $this->url = $baseUrl.$path;
    }

    /**
     * @see CreateAccessTokenActionTest
     */
    public function test_can_get_access_token(): void
    {
        $body = [
            'access_token' => fake()->uuid(),
            'token_type' => 'access_token',
            'expires_in' => 3600,
        ];

        Http::fake([
            $this->url => Http::response($body, 200),
        ]);

        $accessToken = (new GetAccessTokenAction)();

        $this->assertEquals($body['access_token'], $accessToken);
    }
}
