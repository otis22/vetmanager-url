<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url\Part;

use PHPUnit\Framework\TestCase;

class DomainTest extends TestCase
{
    public function testSimpleDomainToString(): void
    {
        $this->assertEquals(
            "myclinic",
            (new Domain("Myclinic"))->asString()
        );
    }

    public function testSimpleDomainToString2(): void
    {
        $this->assertEquals(
            "myclinic",
            (new Domain("MyclInic"))->asString()
        );
    }

    public function testSimpleDomainToString3(): void
    {
        $this->assertEquals(
            "myclinic",
            (new Domain("MycliniC"))->asString()
        );
    }

    /*public function testDomainFromHttpUrlToString(): void
    {
        $this->assertEquals(
            "test",
            (
                new Domain("http://test.vetmanager.ru")
            )->asString()
        );
    }

    public function testDomainFromHttpsUrlToString(): void
    {
        $this->assertEquals(
            "test",
            (
                new Domain("https://test.vetmanager.ru")
            )->asString()
        );
    }

    public function testDomainFromHostNameToString(): void
    {
        $this->assertEquals(
            "test",
            (
                new Domain("test.vetmanager.ru")
            )->asString()
        );
    }*/

    public function testInvalidUrlThrowExceptionToString(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        (
            new Domain("httpfsdfsdtest.")
        )->asString();
    }
}
