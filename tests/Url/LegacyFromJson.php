<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use Otis22\VetmanagerUrl\Url\Part\Domain;

final class LegacyFromJson extends FromJson
{
    public static function fromDomainAndBillingApi(Domain $domain, BillingApi $billingApi): self
    {
        $url = parent::fromDomainAndBillingApi($domain, $billingApi)->asString();
        return new self(json_encode([
            'success' => true,
            'protocol' => 'https',
            'url' => substr($url, strlen('https://')),
        ], JSON_THROW_ON_ERROR));
    }
}
