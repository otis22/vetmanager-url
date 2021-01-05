<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl;

use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerUrl\url;

class UrlTest extends TestCase
{
    public function testValidUrl(): void
    {
        $this->assertStringContainsString(
            'vetmanager',
            url(
                strval(getenv('TEST_DOMAIN_NAME'))
            )->asString()
        );
    }

    public function testInValidUrl(): void
    {
        $this->expectException(\Exception::class);
        url('invalidDomainExact')->asString();
    }
}
