# Vetmanager Url

Vetmanager - CRM for veterinary. All vetmanager clients has access to their crm via URL. Now Vetmanager is changing server architecure and URL can changes. This project for getting full URL by only first part. Use this library avoid hardcode.

Now url address has format $domain.vetmanager.ru for example: myclinic76.vetmanager.ru, but it url format will be change.

![GitHub CI](https://github.com/otis22/vetmanager-url/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/otis22/vetmanager-url/badge.svg?branch=master)](https://coveralls.io/github/otis22/php-skelleton?branch=master)

# How to use 
## Install
```
composer require otis/vetmanager-url
```

## Basic usage Url

```
use function Otis22\VetmanagerUrl\url;

echo url('myclinic') . "\n";
```

Where 'myclinic' is first part from your clinic url. $domain.vetmanager.ru


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
