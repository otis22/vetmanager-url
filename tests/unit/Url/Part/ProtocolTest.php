<?php

namespace Otis22\VetmanagerUrl\Url\Part;

use PHPUnit\Framework\TestCase;

class ProtocolTest extends TestCase
{
    public function testHttpProtocol(): void
    {
        $this->assertEquals(
            "http://",
            (new Protocol("http"))->asString()
        );
    }
}
