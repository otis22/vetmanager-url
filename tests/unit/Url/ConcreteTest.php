<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use PHPUnit\Framework\TestCase;

class ConcreteTest extends TestCase
{

    public function testAsString(): void
    {
        $this->assertEquals(
            "http://test.url",
            (new Concrete("http://test.url"))->asString()
        );
    }
}
