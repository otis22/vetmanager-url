<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use Otis22\VetmanagerUrl\Url\WithURI;
use PHPUnit\Framework\TestCase;

class WithURITest extends TestCase
{
    public function testAsString(): void
    {
        $this->assertEquals(
            "http://test.url/uri/path",
            (
                new WithURI(
                    new Concrete("http://test.url/"),
                    "uri/path"
                )
            )->asString()
        );
    }
}
