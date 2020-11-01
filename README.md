# php skelleton
Skelleton for write high quality php application

![GitHub CI](https://github.com/otis22/vetmanager-url/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/otis22/vetmanager-url/badge.svg?branch=master)](https://coveralls.io/github/otis22/php-skelleton?branch=master)

## Local work

```
cd docker
docker-compose up
```
now you can connect to terminal
```
docker exec -it vetmanager-url /bin/bash
```

## Run tests

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
