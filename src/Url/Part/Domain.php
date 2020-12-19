<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url\Part;

use ElegantBro\Interfaces\Stringify;

final class Domain implements Stringify
{
    /**
     * @var string
     */
    private $urlDomainOrHostName;

    /**
     * Domain constructor.
     * @param string $urlDomainOrHostName
     */
    public function __construct(string $urlDomainOrHostName)
    {
        $this->urlDomainOrHostName = $urlDomainOrHostName;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return $this->isUrl()
            ? $this->domainFromHost($this->hostFromUrl())
            : $this->domainFromHost($this->urlDomainOrHostName);
    }

    /**
     * @return bool
     */
    private function isUrl(): bool
    {
        return strpos($this->urlDomainOrHostName, 'http') === 0
            && strpos($this->urlDomainOrHostName, '.') !== false;
    }

    /**
     * @return string
     */
    private function hostFromUrl(): string
    {
        $parsed = parse_url($this->urlDomainOrHostName);
        if (is_array($parsed) && isset($parsed['host'])) {
            return $parsed['host'];
        }
        throw new \UnexpectedValueException("Can't parse url or host string - " . $this->urlDomainOrHostName);
    }

    /**
     * @param string $host
     * @return string
     */
    private function domainFromHost(string $host): string
    {
        $exploded = explode('.', $host);
        return $exploded[0];
    }
}
