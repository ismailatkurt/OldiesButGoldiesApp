#!/bin/bash

docker exec -it oldies_app_php_fpm /var/www/html/bin/phpunit /var/www/html/tests/unittests/src
