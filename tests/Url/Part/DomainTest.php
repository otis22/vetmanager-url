<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url\Part;

use PHPUnit\Framework\TestCase;

class DomainTest extends TestCase
{
    public function testDomainToString(): void
    {
        $this->assertEquals(
            "test",
            (new Domain("test"))->asString()
        );
    }
}
