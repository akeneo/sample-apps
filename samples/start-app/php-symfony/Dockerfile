FROM php:8.1.1-apache AS core

RUN apt-get update \
    && apt-get install -y \
        libicu-dev \
        libonig-dev \
    && docker-php-ext-install \
        bcmath \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && mv /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

COPY docker/php/php.ini /usr/local/etc/php/conf.d/000-docker.ini
COPY docker/apache/apache2.conf /etc/apache2/apache2.conf
COPY docker/apache/ports.conf /etc/apache2/ports.conf
COPY docker/apache/app.conf /etc/apache2/sites-available/000-default.conf

###############################################################################

FROM core AS dev-tools

ENV COMPOSER_HOME=/tmp

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        curl \
        nodejs \
        npm \
    && npm install -g yarn \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/composer/php.ini /usr/local/etc/php/conf.d/custom.ini

###############################################################################

FROM dev-tools AS vendors

WORKDIR /srv/app
COPY composer.json composer.lock symfony.lock ./
RUN composer install \
        --no-scripts \
        --no-interaction \
        --no-ansi \
        --prefer-dist \
        --optimize-autoloader \
        --no-dev

###############################################################################

FROM dev-tools as development

ARG USER=www-data

RUN pecl install \
        xdebug \
        pcov  \
    && docker-php-ext-enable \
        xdebug \
        pcov

RUN mkdir -p /srv/app && chown $USER /srv/app
WORKDIR /srv/app
USER $USER
