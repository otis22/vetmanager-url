<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use PHPUnit\Framework\TestCase;
use Otis22\VetmanagerUrl\Url\Part\Domain;

final class FromJsonTest extends TestCase
{
    public function testHostNameWithValidUrl(): void
    {
        $this->assertEquals(
            "http://test.fake.url",
            (
                new FromJson(
                    '{
                    "protocol":"http",
                    "host":"test.kube-dev.vetmanager.cloud",
                    "url":"test.fake.url",
                    "success":true
                }'
                )
            )->asString()
        );
    }

    public function testCacheHostName(): void
    {
        $hostName = new FromJson(
            '{
                    "protocol":"http",
                    "host":"test.kube-dev.vetmanager.cloud",
                    "url":"test.fake.url",
                    "success":true
                }'
        );
        $this->assertEquals(
            $hostName->asString(),
            $hostName->asString()
        );
    }
    public function testHostNameWithServerError(): void
    {
        $this->expectException(\Exception::class);
        $url = new FromJson(
            ''
        );
        $url->asString();
    }
    public function testHostNameWithUnsuccess(): void
    {
        $this->expectException(\Exception::class);
        $url = new FromJson(
            '{success: false}'
        );
        $url->asString();
    }

    public function testHostNameWithEmptyUrl(): void
    {
        $this->expectException(\Exception::class);
        $url = new FromJson(
            '{url: "", success: "true"}'
        );
        $url->asString();
    }

    public function testFromDomainAndBillingApi(): void
    {
        $this->expectException(\Exception::class);
        FromJson::fromDomainAndBillingApi(
            new Domain('test'),
            new BillingApi('test')
        );
    }

    public function testFromDomainAndBillingApiDoesNotReportStaleError(): void
    {
        // An earlier suppressed warning, e.g. a missing .env read before the framework boots.
        @\file_get_contents('/nonexistent-dir/.env');
        /** @var array<int, array{int, bool}> $outerCalls */
        $outerCalls = [];
        // Framework-like handler: it handles "@" warnings, so PHP keeps the stale error_get_last().
        $outerHandler = static function (int $level) use (&$outerCalls): bool {
            $outerCalls[] = [$level, (error_reporting() & $level) === 0];
            return true;
        };
        set_error_handler($outerHandler);
        try {
            $message = $this->failureMessage($this->missingGateway());
            $lastError = error_get_last();
            $activeHandler = set_error_handler(null);
            restore_error_handler();
        } finally {
            restore_error_handler();
        }
        self::assertSame('Can`t create FromJson object. Invalid server response. Error: undefined error', $message);
        // Everything else is as before: the handler got the warning under "@",
        // error_get_last() is left untouched, the handler stack is unchanged.
        self::assertSame([[E_WARNING, true]], $outerCalls);
        self::assertStringContainsString('/nonexistent-dir/.env', $lastError['message'] ?? '');
        self::assertSame($outerHandler, $activeHandler);
    }

    public function testFromDomainAndBillingApiReportsCurrentWarning(): void
    {
        @\file_get_contents('/nonexistent-dir/.env');
        set_error_handler(null);
        try {
            $message = $this->failureMessage($this->missingGateway());
            $lastError = error_get_last();
        } finally {
            restore_error_handler();
        }
        self::assertSame(
            'Can`t create FromJson object. Invalid server response. Error: ' . ($lastError['message'] ?? ''),
            $message
        );
        self::assertStringContainsString('/host/clinic): ', $message);
        self::assertStringContainsStringIgnoringCase('failed to open stream: no such file or directory', $message);
        $this->expectOutputString('');
    }

    public function testFromDomainAndBillingApiPropagatesPreviousHandlerException(): void
    {
        $outerHandler = static function (int $level, string $message): bool {
            throw new \RuntimeException('outer: ' . $message);
        };
        set_error_handler($outerHandler);
        StreamTransport::$fail = true;
        StreamTransport::$warnings = ['Failed to open stream: Connection refused'];
        $exception = null;
        try {
            FromJson::fromDomainAndBillingApi(new Domain('clinic'), new BillingApi('https://billing.example'));
        } catch (\RuntimeException $e) {
            $exception = $e;
        } finally {
            $activeHandler = set_error_handler(null);
            restore_error_handler();
            restore_error_handler();
            StreamTransport::reset();
        }
        self::assertSame(
            'outer: Failed to open stream: Connection refused',
            $exception === null ? null : $exception->getMessage()
        );
        self::assertSame($outerHandler, $activeHandler);
    }

    public function testFromDomainAndBillingApiWithoutWarning(): void
    {
        StreamTransport::$fail = true;
        try {
            $message = $this->failureMessage(new BillingApi('https://billing.example'));
            $requests = StreamTransport::$requests;
        } finally {
            StreamTransport::reset();
        }
        self::assertSame('Can`t create FromJson object. Invalid server response. Error: undefined error', $message);
        self::assertSame(['https://billing.example/host/clinic'], $requests);
    }

    public function testFromDomainAndBillingApiReportsSocketTimeout(): void
    {
        $server = stream_socket_server('tcp://127.0.0.1:0');
        if (!is_resource($server)) {
            throw new \RuntimeException('Cannot start a local TCP server');
        }
        $address = stream_socket_get_name($server, false);
        self::assertIsString($address);
        $timeout = ini_set('default_socket_timeout', '1');
        try {
            // The server accepts the connection into its backlog but never answers.
            $message = $this->failureMessage(new BillingApi('http://' . $address));
        } finally {
            ini_set('default_socket_timeout', (string) $timeout);
            fclose($server);
        }
        self::assertStringContainsString('http://' . $address . '/host/clinic', $message);
        self::assertStringStartsWith(
            'Can`t create FromJson object. Invalid server response. Error: file_get_contents(http://',
            $message
        );
        self::assertStringContainsStringIgnoringCase('failed to open stream', $message);
    }

    private function missingGateway(): BillingApi
    {
        return new BillingApi('file://' . sys_get_temp_dir() . '/vetmanager-url-missing-' . uniqid());
    }

    private function failureMessage(BillingApi $billingApi): string
    {
        try {
            FromJson::fromDomainAndBillingApi(new Domain('clinic'), $billingApi);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        self::fail('Exception expected');
    }
}
