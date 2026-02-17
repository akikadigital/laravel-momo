<?php

namespace Akika\MoMo\Tests\Actions\Disbursement;

use Akika\MoMo\Actions\Disbursment\CreateAccessTokenAction;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateAccessTokenActionTest extends TestCase
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

    public function test_can_create_access_token(): void
    {
        $body = [
            'access_token' => fake()->uuid(),
            'token_type' => 'access_token',
            'expires_in' => 3600,
        ];

        Http::fake([
            $this->url => Http::response($body, 200),
        ]);

        $response = (new CreateAccessTokenAction)();

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertEquals($this->url, $request->url());
        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));

        $token = base64_encode("{$this->xReferenceId}:{$this->apiKey}");
        $this->assertEquals("Basic {$token}", $request->header('Authorization')[0]);
        $this->assertEquals($body, $response);
    }
}
