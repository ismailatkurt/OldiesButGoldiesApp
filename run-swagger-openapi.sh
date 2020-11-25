#!/bin/bash

docker exec -it oldies_app_php_fpm /var/www/html/bin/openapi /var/www/html/src -o /var/www/html/public/swagger.json
