#!/bin/bash

docker exec -it oldies_app_php_fpm /var/www/html/bin/behat -c /var/www/html/tests/acceptancetests/behat.yml /var/www/html/tests/acceptancetests/features/
