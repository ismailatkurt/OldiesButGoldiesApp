#!/bin/bash

cd /var/www/html

composer install

php ./bin/phpunit ./tests/unittests/src

php ./bin/phinx migrate -e development

php ./bin/phinx seed:run -e development

./bin/openapi ./src/ -o ./public/swagger.json

exec "$@"
