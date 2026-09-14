[![GitHub CI](https://github.com/otis22/vetmanager-url/workflows/CI/badge.svg)](https://github.com/otis22/vetmanager-url/actions/workflows/CI.yml?query=branch%3Amain)
[![Coverage Status](https://coveralls.io/repos/github/otis22/vetmanager-url/badge.svg?branch=main)](https://coveralls.io/github/otis22/vetmanager-url?branch=main)

# Vetmanager Url

Vetmanager - CRM for veterinary. All vetmanager clients has access to their crm via URL. Now Vetmanager is changing server architecure and URL can changes. This project for getting full URL by only first part. Use this library avoid hardcode.

Now url address has format $domain.vetmanager.ru for example: myclinic76.vetmanager.ru, but it url format will be change.


[Vetmanager REST API Docs](https://vetmanager.ru/knowledgebase/rest-api-osnovnaya-informatsia)

[Vetmanager REST API in Postman](https://god.postman.co/run-collection/64d692ca1ea129218ccb)

# How to use 
## Installation
```
composer require otis22/vetmanager-url
```
## Examples
```php
use function Otis22\VetmanagerUrl\url;

/*
    return Url object, which can be convert to string
    with full url address https://$domain.vetmanager.ru
*/
echo url('myclinic')->asString();
```
Where 'myclinic' is first part from your clinic url. $domain.vetmanager.ru and "vetmanager.ru" is a variable

## Optional HTTP client

Existing calls such as `url('myclinic')` continue to use PHP streams and do not
require Guzzle. To configure outgoing requests, install Guzzle 6.5 or 7:

```shell
composer require guzzlehttp/guzzle
```

Pass a `GuzzleHttp\ClientInterface` implementation as the optional client:

```php
use GuzzleHttp\Client;
use function Otis22\VetmanagerUrl\url;
use function Otis22\VetmanagerUrl\url_test_env;
use function Otis22\VetmanagerUrl\create_url_from_billing_api_gateway;

$client = new Client([
    'headers' => ['User-Agent' => 'my-project/1.2.3'],
    'connect_timeout' => 3,
    'timeout' => 10,
]);

echo url('myclinic', $client)->asString();
echo url_test_env('myclinic', $client)->asString();
echo create_url_from_billing_api_gateway(
    'myclinic',
    'https://billing-api.example',
    $client
)->asString();
```

The client is also accepted as the third argument of
`Url\FromJson::fromDomainAndBillingApi($domain, $billingApi, $client)`.
All previously required arguments and return types remain unchanged. Passing
`null` is equivalent to omitting the client.

Compatibility note for subclasses: if you override
`FromJson::fromDomainAndBillingApi()`, add the optional
`?GuzzleHttp\ClientInterface $client = null` parameter to that override as well.
An override with the old two-parameter signature is incompatible with the new
parent signature. Existing function calls do not need this change.

The library sends `GET /host/<domain>` through the supplied client. It does not
replace its headers, timeouts, proxy or middleware configuration. The library
does not invent a project name or version; the calling application owns them.
Transport exceptions propagate. HTTP responses with status 400 or higher are
rejected even when the client sets `http_errors` to `false`. Existing JSON and
billing-response validation still applies when `asString()` is called.

`url()` without a client keeps its existing in-process cache by domain. Calls
with an explicit client bypass that shared cache: each function call performs
a request, even for the same domain and client. This prevents an earlier lookup
from hiding a request made with another client's configuration. Keep the returned
`Url` object if you want to reuse the result; repeated `asString()` calls do not
send additional requests. `url_test_env()` and the custom-gateway factory remain
uncached. Gateway URLs with a trailing slash are accepted without generating a
double slash before `/host/`. This normalization also applies to existing
calls without a client; previously they could send `//host/`.

When another library calls these helpers, it must accept and forward the client
from the application. Use a client configured for billing URL discovery, separate
from the CRM client carrying clinic credentials. Installing this release alone
does not add project headers to existing callers.

## Contributing

The CI matrix tests the optional client with both Guzzle 6 and Guzzle 7.
Guzzle 6.5.8 is tested only for legacy compatibility, using mock HTTP handlers.
Known advisories for Guzzle 6.5.8 and its PSR-7 1.9.1 dependency are allowed only
during dependency installation in those CI jobs and remain visible to audit. Live integration tests and coverage uploads
run only with Guzzle 7. This CI exception does not apply to consumer installations;
use a current Guzzle 7 release for new integrations.
The minimum supported PHP version remains 7.4. CI also verifies a production
installation without Guzzle and runs unit tests in a fixed shuffled order.

For run all tests
```shell
make all
```
or connect to terminal
```shell
make exec
```
*Dafault php version is 8.2*. Use PHP_VERSION= for using custom version.
```shell
make all PHP_VERSION=8.0
# run both 
make all PHP_VERSION=7.4 && make all PHP_VERSION=8.0
```

*For integration tests copy .env.example to .env and fill with yours values*

all commands
```shell
# security check
make security
# composer install
make install
# composer install with --no-dev
make install-no-dev
# check code style
make style
# run static analyze tools
make static-analyze
# run unit tests
make unit
#  check coverage
make coverage
# check integration, .env required
make integration
```