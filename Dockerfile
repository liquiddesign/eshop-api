FROM php:8.1-apache as base

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y libicu-dev
RUN docker-php-ext-install intl

RUN apt install -y libxml2-dev
RUN docker-php-ext-install soap

RUN apt install -y libzip-dev
RUN docker-php-ext-install zip

RUN docker-php-ext-install bcmath

RUN apt install -y libgd-dev
RUN docker-php-ext-install gd

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

COPY . "/var/www/html/"
RUN mkdir /var/www/html/temp/log
RUN mkdir /var/www/html/temp/cache
RUN chmod -R a+rw /var/www/html/temp

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get install nano
RUN composer install --no-dev
