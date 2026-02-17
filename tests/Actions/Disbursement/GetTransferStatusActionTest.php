<?php

namespace Akika\MoMo\Tests\Actions\Disbursement;

use Akika\MoMo\Actions\Disbursment\GetTransferStatusAction;
use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetTransferStatusActionTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $xReferenceId;

    public string $callbackHost;

    public string $callbackUrl;

    public string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.disbursement.url_paths.get_transfer_status');
        $this->url = $baseUrl.$path;
    }

    public function test_can_get_transfer_status(): void
    {
        $body = [
            'amount' => fake()->randomNumber(),
            'currency' => fake()->randomElement(Currency::cases())->value,
            'financialTransactionId' => fake()->uuid(),
            'externalId' => fake()->uuid(),
            'payee' => [
                'partyIdType' => 'MSISDN',
                'partyId' => fake()->randomNumber(),
            ],
            'payerMessage' => fake()->sentence(),
            'payeeNote' => fake()->sentence(),
            'status' => 'SUCCESSFUL',
        ];

        $accessToken = fake()->uuid();
        $tReferenceId = fake()->uuid();
        $this->url = str_replace('{referenceId}', $tReferenceId, $this->url);

        Http::fake([
            $this->url => Http::response($body, 200),
        ]);

        $response = (new GetTransferStatusAction)($accessToken, $tReferenceId);

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertTrue($request->hasHeader('X-Target-Environment', $this->env));
        $this->assertEquals("Bearer {$accessToken}", $request->header('Authorization')[0]);

        $this->assertEquals($body, $response);
    }
}
