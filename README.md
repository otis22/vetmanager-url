![GitHub CI](https://github.com/otis22/vetmanager-url/workflows/CI/badge.svg)
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

## Contributing

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