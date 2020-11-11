![GitHub CI](https://github.com/otis22/vetmanager-url/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/otis22/vetmanager-url/badge.svg?branch=main)](https://coveralls.io/github/otis22/vetmanager-url?branch=main)

# Vetmanager Url

Vetmanager - CRM for veterinary. All vetmanager clients has access to their crm via URL. Now Vetmanager is changing server architecure and URL can changes. This project for getting full URL by only first part. Use this library avoid hardcode.

Now url address has format $domain.vetmanager.ru for example: myclinic76.vetmanager.ru, but it url format will be change.


[Vetmanager REST API Docs](https://vetmanager.ru/knowledgebase/rest-api-osnovnaya-informatsia)

[Vetmanager REST API in Postman](https://god.postman.co/run-collection/64d692ca1ea129218ccb)

# How to use 
## Install
```
composer require otis22/vetmanager-url
```
## Examples
```
use function Otis22\VetmanagerUrl\url;

/*
    return Url object, which can be convert to string
    with full url address https://$domain.vetmanager.ru
*/
echo url('myclinic') . "\n";
```
Where 'myclinic' is first part from your clinic url. $domain.vetmanager.ru and "vetmanager.ru" is a variable

## For contributors
### Local work
```
cd docker
docker-compose up
```
now you can connect to terminal
```
docker exec -it vetmanager-url /bin/bash
```
### Run tests
```
#run all
composer check-all

#security check
composer security

#check code style
composer check-style

#analyze code
composer check-static-analyze

#run unit tests
composer unit
```
