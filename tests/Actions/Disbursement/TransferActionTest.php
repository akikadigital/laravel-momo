<?php

namespace Akika\MoMo\Tests\Actions\Disbursement;

use Akika\MoMo\Actions\Disbursment\TransferAction;
use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TransferActionTest extends TestCase
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

        Config::set('momo.disbursement.callback_url', $this->callbackUrl = fake()->url());

        $path = Config::string('momo.disbursement.url_paths.transfer');
        $this->url = $baseUrl.$path;
    }

    public function test_can_initiate_a_transfer(): void
    {
        Http::fake([
            $this->url => Http::response(null, 202),
        ]);

        $accessToken = fake()->uuid();
        $amount = fake()->randomFloat(2, 5, 10_000);
        $currency = fake()->randomElement(Currency::cases());
        $externalId = fake()->uuid();
        $payeeMsisdn = fake()->phoneNumber();
        $payerMessage = fake()->sentence();
        $payeeNote = fake()->sentence();

        $tReferenceId = (new TransferAction)(
            $accessToken,
            $amount,
            $currency,
            $externalId,
            $payeeMsisdn,
            $payerMessage,
            $payeeNote
        );

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertEquals($this->url, $request->url());
        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertTrue($request->hasHeader('X-Callback-Url', $this->callbackUrl));
        $this->assertTrue($request->hasHeader('X-Reference-Id', $tReferenceId));
        $this->assertTrue($request->hasHeader('X-Target-Environment', $this->env));
        $this->assertEquals("Bearer {$accessToken}", $request->header('Authorization')[0]);

        $this->assertTrue(Str::isUuid($tReferenceId));
        $this->assertEquals(number_format($amount, 2, thousands_separator: ''), $request['amount']);
        $this->assertEquals($currency->value, $request['currency']);
        $this->assertEquals($externalId, $request['externalId']);
        $this->assertEquals('MSISDN', $request['payee']['partyIdType']);
        $this->assertEquals($payeeMsisdn, $request['payee']['partyId']);
        $this->assertEquals($payerMessage, $request['payerMessage']);
        $this->assertEquals($payeeNote, $request['payeeNote']);
    }

    public function test_discards_null_payload(): void
    {
        Http::fake([
            $this->url => Http::response(null, 202),
        ]);

        $accessToken = fake()->uuid();
        $amount = fake()->randomFloat(2, 5, 10_000);
        $currency = fake()->randomElement(Currency::cases());

        $tReferenceId = (new TransferAction)(
            $accessToken,
            $amount,
            $currency,
        );

        /** @var Request */
        $request = null;
        Http::assertSent(function (Request $sentRequest) use (&$request) {
            $request = $sentRequest;

            return true;
        });

        $this->assertEquals($this->url, $request->url());
        $this->assertTrue($request->hasHeader('Ocp-Apim-Subscription-Key', $this->secondaryKey));
        $this->assertTrue($request->hasHeader('X-Callback-Url', $this->callbackUrl));
        $this->assertTrue($request->hasHeader('X-Reference-Id', $tReferenceId));
        $this->assertTrue($request->hasHeader('X-Target-Environment', $this->env));
        $this->assertEquals("Bearer {$accessToken}", $request->header('Authorization')[0]);

        $this->assertTrue(Str::isUuid($tReferenceId));
        $this->assertEquals(number_format($amount, 2, thousands_separator: ''), $request['amount']);
        $this->assertEquals($currency->value, $request['currency']);

        $expected = [
            'amount' => number_format($amount, 2, thousands_separator: ''),
            'currency' => $currency->value,
        ];
        $this->assertEquals($expected, $request->data());
    }
}
