<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url\Part;

use ElegantBro\Interfaces\Stringify;
use Exception;

final class Protocol implements Stringify
{
    /**
     * @var string
     */
    private $protocol;

    /**
     * Protocol constructor.
     *
     * @param string $protocol
     */
    public function __construct(string $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->protocol . "://";
    }
}
