<?php

namespace Akika\MoMo\Tests\Actions\Disbursement;

use Akika\MoMo\Actions\Disbursment\GetAccountBalanceAction;
use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetAccountBalanceActionTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", fake()->uuid());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.disbursement.url_paths.get_account_balance');
        $this->url = $baseUrl.$path;
    }

    public function test_can_get_account_balance(): void
    {
        // ===========================================================================
        // Initialize data
        // ===========================================================================
        $body = [
            'availableBalance' => strval(fake()->randomNumber(4)),
            'currency' => fake()->randomElement(Currency::cases())->value,
        ];
        $accessToken = fake()->uuid();

        // ===========================================================================
        // Setup the environment
        // ===========================================================================
        Http::fake([
            $this->url => Http::response($body, 200),
        ]);

        // ===========================================================================
        // Run the block of code in question
        // ===========================================================================
        $response = (new GetAccountBalanceAction)($accessToken);

        // ===========================================================================
        // Make assertions
        // ===========================================================================
        $this->assertEquals($body, $response);

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertTrue($request->hasHeader('X-Target-Environment', $this->env));
        $this->assertEquals("Bearer {$accessToken}", $request->header('Authorization')[0]);
    }
}
