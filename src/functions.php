<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl;

use GuzzleHttp\ClientInterface;
use Otis22\VetmanagerUrl\Url\Part\Domain;

function create_url_from_billing_api_gateway(
    string $domainName,
    string $billingApiUrl,
    ?ClientInterface $client = null
): Url {
    return Url\FromJson::fromDomainAndBillingApi(
        new Domain($domainName),
        new Url\BillingApi($billingApiUrl),
        $client
    );
}

function url(string $domainName, ?ClientInterface $client = null): Url
{
    if ($client !== null) {
        return create_url_from_billing_api_gateway($domainName, "https://billing-api.vetmanager.ru", $client);
    }

    /** @var array<string, Url> $cache */
    static $cache = [];

    if (isset($cache[$domainName])) {
        return $cache[$domainName];
    }

    $cache[$domainName] = create_url_from_billing_api_gateway(
        $domainName,
        "https://billing-api.vetmanager.ru"
    );

    return $cache[$domainName];
}

function url_test_env(string $domainName, ?ClientInterface $client = null): Url
{
    return create_url_from_billing_api_gateway(
        $domainName,
        "https://billing-api-test.kube-dev.vetmanager.cloud/",
        $client
    );
}
