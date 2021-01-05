<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Otis22\VetmanagerUrl\Url\Part\Domain;

final class FromBillingApiGatewayTest extends TestCase
{
    public function testHostNameWithValidUrl(): void
    {
        $mock = new MockHandler(
            [
            new Response(
                200,
                [],
                '{
                    "protocol":"http",
                    "host":"test.kube-dev.vetmanager.cloud",
                    "url":"test.fake.url",
                    "success":true
                }'
            )
            ]
        );
        $handlerStack = HandlerStack::create($mock);

        $this->assertEquals(
            "http://test.fake.url",
            (
                new FromBillingApiGateway(
                    new BillingApi("https://fake.billing.url"),
                    new Domain('one'),
                    new Client(['handler' => $handlerStack])
                )
            )->asString()
        );
    }

    public function testCacheHostName(): void
    {
        $mock = new MockHandler(
            [
            new Response(
                200,
                [],
                '{
                    "protocol":"http",
                    "host":"test.kube-dev.vetmanager.cloud",
                    "url":"test.fake.url",
                    "success":true
                }'
            )
            ]
        );
        $handlerStack = HandlerStack::create($mock);
        $hostName = new FromBillingApiGateway(
            new BillingApi("https://fake.billing.url"),
            new Domain('one'),
            new Client(['handler' => $handlerStack])
        );
        $this->assertEquals(
            $hostName->asString(),
            $hostName->asString()
        );
    }
    public function testHostNameWithServerError(): void
    {
        $this->expectException(\Exception::class);
        $mock = new MockHandler(
            [
            new Response(
                500,
                [],
                ''
            )
            ]
        );
        $handlerStack = HandlerStack::create($mock);
        $url = new FromBillingApiGateway(
            new BillingApi("https://fake.billing.url"),
            new Domain('one'),
            new Client(['handler' => $handlerStack])
        );
        $url->asString();
    }
    public function testHostNameWithUnsuccess(): void
    {
        $this->expectException(\Exception::class);
        $mock = new MockHandler(
            [
            new Response(
                500,
                [],
                '{success: false}'
            )
            ]
        );
        $handlerStack = HandlerStack::create($mock);
        $url = new FromBillingApiGateway(
            new BillingApi("https://fake.billing.url"),
            new Domain('one'),
            new Client(['handler' => $handlerStack])
        );
        $url->asString();
    }

    public function testHostNameWithEmptyUrl(): void
    {
        $this->expectException(\Exception::class);
        $mock = new MockHandler(
            [
            new Response(
                200,
                [],
                '{url: "", success: "true"}'
            )
            ]
        );
        $handlerStack = HandlerStack::create($mock);
        $url = new FromBillingApiGateway(
            new BillingApi("https://fake.billing.url"),
            new Domain('one'),
            new Client(['handler' => $handlerStack])
        );
        $url->asString();
    }
}
