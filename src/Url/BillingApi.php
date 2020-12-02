<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use ElegantBro\Interfaces\Stringify;

final class BillingApi implements Stringify
{
    /**
     * @var string
     */
    private $billingApiUrl;

    /**
     * BillingUrl constructor.
     *
     * @param string $billingApiUrl
     */
    public function __construct(string $billingApiUrl)
    {
        $this->billingApiUrl = $billingApiUrl;
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->billingApiUrl;
    }
}
