<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testUrlReturnInstanceOfUrl(): void
    {
        $this->assertTrue(url('test') instanceof Url);
    }

    public function testUrlReturnInstanceOfUrlForTestEnv(): void
    {
        $this->assertTrue(url_test_env('test') instanceof Url);
    }
}
