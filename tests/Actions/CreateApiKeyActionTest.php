<?php

namespace Akika\MoMo\Tests\Actions;

use Akika\MoMo\Actions\CreateApiKeyAction;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiKeyActionTest extends TestCase
{
    public string $secondaryKey;

    public string $callbackHost;

    public string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $xReferenceId = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.url_paths.create_api_key');
        $path = str_replace('{referenceId}', $xReferenceId, $path);
        $this->url = $baseUrl.$path;
    }

    public function test_can_create_api_keys(): void
    {
        $body = ['apiKey' => fake()->uuid()];
        Http::fake([
            $this->url => Http::response($body, 201),
        ]);

        $apiKey = (new CreateApiKeyAction)();

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertEquals($this->url, $request->url());
        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertEquals($body['apiKey'], $apiKey);
    }
}
