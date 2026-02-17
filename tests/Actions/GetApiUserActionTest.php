<?php

namespace Akika\MoMo\Tests\Actions;

use Akika\MoMo\Actions\GetApiUserAction;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetApiUserActionTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $callbackHost;

    public string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $xReferenceId = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.url_paths.get_api_user');
        $path = str_replace('{referenceId}', $xReferenceId, $path);
        $this->url = $baseUrl.$path;
    }

    public function test_can_get_api_user(): void
    {
        Http::fake([
            $this->url => Http::response([
                'providerCallbackHost' => $this->callbackHost,
                'targetEnvironment' => $this->env,
            ], 200),
        ]);

        $response = (new GetApiUserAction)();

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertEquals($this->url, $request->url());
        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertEquals($this->url, $request->url());
        $this->assertEquals($this->callbackHost, $response['providerCallbackHost']);
        $this->assertEquals($this->env, $response['targetEnvironment']);
    }
}
