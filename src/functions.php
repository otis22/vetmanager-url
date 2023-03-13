<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl;

use GuzzleHttp\Client;
use Otis22\VetmanagerUrl\Url\Part\Domain;

function create_url_from_billing_api_gateway(string $domainName, string $billingApiUrl): Url
{
    return Url\FromJson::fromDomainAndBillingApi(
        new Domain($domainName),
        new Url\BillingApi($billingApiUrl)
    );
}

function url(string $domainName): Url
{
    return create_url_from_billing_api_gateway($domainName, "https://billing-api.vetmanager.cloud");
}

function url_test_env(string $domainName): Url
{
    return create_url_from_billing_api_gateway($domainName, "https://billing-api-test.kube-dev.vetmanager.cloud/");
}
