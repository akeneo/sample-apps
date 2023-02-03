FROM php:8.1.1-apache AS core

RUN apt-get update \
    && apt-get install -y \
            git \
            unzip \
            curl \
    && apt-get clean \
    && a2enmod rewrite

COPY docker/php/php.ini /usr/local/etc/php/conf.d/000-docker.ini
COPY docker/apache/apache2.conf /etc/apache2/apache2.conf
COPY docker/apache/ports.conf /etc/apache2/ports.conf
COPY docker/apache/app.conf /etc/apache2/sites-available/000-default.conf

###############################################################################

FROM core AS dev-tools

ENV COMPOSER_HOME=/tmp

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/composer/php.ini /usr/local/etc/php/conf.d/custom.ini

###############################################################################

FROM dev-tools as development

ARG USER=www-data

RUN mkdir -p /srv/app && chown $USER /srv/app
WORKDIR /srv/app
USER $USER
