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
}
