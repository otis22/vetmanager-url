<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use Otis22\VetmanagerUrl\Url;

final class Concrete implements Url
{
    /**
     * @var string
     */
    private $url;

    /**
     * Concrete constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return $this->url;
    }
}
