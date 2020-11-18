FROM php:7.4-fpm

WORKDIR /var/www/html

RUN apt-get update -y

RUN apt-get install git libzip-dev nano zip unzip libpq-dev postgresql -y

RUN docker-php-ext-install pdo pdo_pgsql pgsql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN pecl install -o -f redis
RUN rm -rf /tmp/pear
RUN docker-php-ext-enable redis

COPY ./init.sh /usr/local/bin
