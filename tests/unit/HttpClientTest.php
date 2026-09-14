<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Otis22\VetmanagerUrl\Url\StreamTransport;
use Otis22\VetmanagerUrl\Url\BillingApi;
use Otis22\VetmanagerUrl\Url\FromJson;
use Otis22\VetmanagerUrl\Url\Part\Domain;
use PHPUnit\Framework\TestCase;

final class HttpClientTest extends TestCase
{
    private const JSON = '{"success":true,"protocol":"https","url":"clinic.example"}';

    /** @return array<string, array{string, string}> */
    public function entryPoints(): array
    {
        return [
            'production' => ['production', 'https://billing-api.vetmanager.ru/host/clinic'],
            'test environment' => ['test', 'https://billing-api-test.kube-dev.vetmanager.cloud/host/clinic'],
            'custom gateway' => ['custom', 'https://billing.example/prefix/host/clinic'],
            'direct factory' => ['factory', 'https://billing.example/prefix/host/clinic'],
        ];
    }

    /** @dataProvider entryPoints */
    public function testConfiguredClientReachesEveryEntryPoint(string $entryPoint, string $expectedUrl): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new Response(200, [], self::JSON)]));
        $handler->push(Middleware::history($history));
        $client = new Client([
            'handler' => $handler,
            'headers' => ['User-Agent' => 'test-project/1.2.3'],
            'connect_timeout' => 3,
            'timeout' => 10,
        ]);

        if ($entryPoint === 'production') {
            $result = url('Clinic', $client);
        } elseif ($entryPoint === 'test') {
            $result = url_test_env('Clinic', $client);
        } elseif ($entryPoint === 'custom') {
            $result = create_url_from_billing_api_gateway('Clinic', 'https://billing.example/prefix/', $client);
        } else {
            $result = FromJson::fromDomainAndBillingApiUsingClient(
                new Domain('Clinic'),
                new BillingApi('https://billing.example/prefix/'),
                $client
            );
        }

        self::assertSame('https://clinic.example', $result->asString());
        self::assertSame('https://clinic.example', $result->asString());
        self::assertCount(1, $history);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame($expectedUrl, (string) $history[0]['request']->getUri());
        self::assertSame('test-project/1.2.3', $history[0]['request']->getHeaderLine('User-Agent'));
        self::assertSame(3, $history[0]['options']['connect_timeout']);
        self::assertSame(10, $history[0]['options']['timeout']);
    }

    public function testClientsDoNotShareDomainCache(): void
    {
        $first = new Client(['handler' => new MockHandler([new Response(200, [], self::JSON)])]);
        $second = new Client(['handler' => new MockHandler([
            new Response(200, [], str_replace('clinic.example', 'other.example', self::JSON)),
        ])]);

        self::assertSame('https://clinic.example', url('client-isolation', $first)->asString());
        self::assertSame('https://other.example', url('client-isolation', $second)->asString());
    }

    public function testExplicitClientBypassesCacheOnEveryCall(): void
    {
        $client = new Client(['handler' => new MockHandler([
            new Response(200, [], self::JSON),
            new Response(200, [], str_replace('clinic.example', 'updated.example', self::JSON)),
        ])]);

        self::assertSame('https://clinic.example', url('fresh-result', $client)->asString());
        self::assertSame('https://updated.example', url('fresh-result', $client)->asString());
    }

    public function testTransportFailureDoesNotPoisonLaterCalls(): void
    {
        $client = new Client(['handler' => new MockHandler([
            new ConnectException('Connection failed', new Request('GET', 'https://billing.example')),
            new Response(200, [], self::JSON),
        ])]);

        try {
            url('retry-result', $client);
            self::fail('A transport failure must propagate.');
        } catch (ConnectException $exception) {
            self::assertSame('Connection failed', $exception->getMessage());
        }
        self::assertSame('https://clinic.example', url('retry-result', $client)->asString());
    }

    public function testHttpErrorIsRejectedWhenClientDisablesHttpErrors(): void
    {
        $client = new Client([
            'handler' => new MockHandler([new Response(503, [], self::JSON)]),
            'http_errors' => false,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Billing API HTTP error: 503');
        url('http-error', $client);
    }

    /** @return array<string, array{string}> */
    public function invalidResponses(): array
    {
        return [
            'invalid JSON' => ['not JSON'],
            'unsuccessful' => ['{"success":false,"protocol":"https","url":"clinic.example"}'],
            'empty URL' => ['{"success":true,"protocol":"https","url":""}'],
            'empty protocol' => ['{"success":true,"protocol":"","url":"clinic.example"}'],
        ];
    }

    /** @dataProvider invalidResponses */
    public function testResponseValidationIsPreserved(string $body): void
    {
        $client = new Client(['handler' => new MockHandler([new Response(200, [], $body)])]);
        $this->expectException(\Exception::class);
        url('invalid-response', $client)->asString();
    }

    public function testDefaultCacheAndExplicitClientStayIndependent(): void
    {
        $domain = 'cache-isolation-' . bin2hex(random_bytes(8));
        StreamTransport::$requests = [];
        StreamTransport::$response = '{"success":true,"protocol":"https","url":"default.example"}';
        try {
            $default = url($domain);
            $client = new Client(['handler' => new MockHandler([new Response(200, [], self::JSON)])]);

            self::assertSame('https://default.example', $default->asString());
            self::assertSame('https://clinic.example', url($domain, $client)->asString());
            self::assertSame($default, url($domain, null));
            self::assertSame('https://default.example', url($domain)->asString());
            self::assertSame(['https://billing-api.vetmanager.ru/host/' . $domain], StreamTransport::$requests);
        } finally {
            StreamTransport::$response = null;
            StreamTransport::$requests = [];
        }
    }

    /** @return array<string, array{string}> */
    public function gatewaySuffixes(): array
    {
        return ['without slash' => [''], 'with slash' => ['/']];
    }

    /** @dataProvider gatewaySuffixes */
    public function testDefaultTransportStillReadsWithoutClient(string $suffix): void
    {
        $directory = sys_get_temp_dir() . '/vetmanager-url-' . bin2hex(random_bytes(8));
        mkdir($directory . '/host', 0700, true);
        file_put_contents($directory . '/host/clinic', self::JSON);

        try {
            self::assertSame(
                'https://clinic.example',
                create_url_from_billing_api_gateway('clinic', 'file://' . $directory . $suffix)->asString()
            );
        } finally {
            unlink($directory . '/host/clinic');
            rmdir($directory . '/host');
            rmdir($directory);
        }
    }
}
