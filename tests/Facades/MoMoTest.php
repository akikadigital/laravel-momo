<?php

namespace Akika\MoMo\Tests\Facades;

use Akika\MoMo\Facades\MoMo;
use Akika\MoMo\Products\Disbursement;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class MoMoTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $xReferenceId;

    public string $callbackHost;

    public string $baseMomoUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $this->baseMomoUrl = fake()->url());

    }

    public function test_can_create_api_users(): void
    {
        // ===========================================================================
        // Initialize data
        // ===========================================================================
        $path = Config::string('momo.url_paths.create_api_user');
        $url = $this->baseMomoUrl.$path;

        // ===========================================================================
        // Setup the environment
        // ===========================================================================
        Http::fake([
            $url => Http::response('', 201),
        ]);

        // ===========================================================================
        // Run the block of code in question
        // ===========================================================================
        MoMo::createApiUser();

        // ===========================================================================
        // Make assertions
        // ===========================================================================
        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertEquals($url, $request->url());
        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertTrue($request->hasHeader('X-Reference-Id', $this->xReferenceId));
        $this->assertEquals($this->callbackHost, $request['providerCallbackHost']);
    }

    public function test_can_get_api_user(): void
    {
        // ===========================================================================
        // Initialize data
        // ===========================================================================
        $path = Config::string('momo.url_paths.get_api_user');
        $path = str_replace('{referenceId}', $this->xReferenceId, $path);
        $url = $this->baseMomoUrl.$path;

        // ===========================================================================
        // Setup the environment
        // ===========================================================================
        Http::fake([
            $url => Http::response([
                'providerCallbackHost' => $this->callbackHost,
                'targetEnvironment' => $this->env,
            ], 200),
        ]);

        // ===========================================================================
        // Run the block of code in question
        // ===========================================================================
        $response = MoMo::getApiUser();

        // ===========================================================================
        // Make assertions
        // ===========================================================================
        $this->assertEquals($this->callbackHost, $response['providerCallbackHost']);
        $this->assertEquals($this->env, $response['targetEnvironment']);
    }

    public function test_can_create_api_key(): void
    {
        // ===========================================================================
        // Initialize data
        // ===========================================================================
        $path = Config::string('momo.url_paths.create_api_key');
        $path = str_replace('{referenceId}', $this->xReferenceId, $path);
        $url = $this->baseMomoUrl.$path;
        $body = ['apiKey' => fake()->uuid()];

        // ===========================================================================
        // Setup the environment
        // ===========================================================================
        Http::fake([
            $url => Http::response($body, 201),
        ]);

        // ===========================================================================
        // Run the block of code in question
        // ===========================================================================
        $apiKey = MoMo::createApiKey();

        // ===========================================================================
        // Make assertions
        // ===========================================================================
        $this->assertEquals($body['apiKey'], $apiKey);
    }

    public function test_returns_disbursment_class_instance(): void
    {
        $disbursement = MoMo::disbursement();

        $this->assertInstanceOf(Disbursement::class, $disbursement);
    }
}
