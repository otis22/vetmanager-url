<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url\Part;

use ElegantBro\Interfaces\Stringify;

final class Domain implements Stringify
{
    /**
     * @var string
     */
    private $domain;

    /**
     * Domain constructor.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->domain;
    }
}
