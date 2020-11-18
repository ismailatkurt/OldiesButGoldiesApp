#!/bin/bash

cd /var/www/html

composer install

#php ./bin/phinx migrate -e development

#php ./bin/phinx seed:run -e development

exec "$@"
