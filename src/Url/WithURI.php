<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use Otis22\VetmanagerUrl\Url;

final class WithURI implements Url
{
    /**
     * @var Url
     */
    private $url;
    /**
     * @var string
     */
    private $uri;

    /**
     * UrlWithURI constructor.
     * @param Url $url
     * @param string $uri
     */
    public function __construct(Url $url, string $uri)
    {
        $this->url = $url;
        $this->uri = $uri;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return $this->url->asString()
            . $this->uri;
    }
}
