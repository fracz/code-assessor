FROM php:7.0.26-apache

RUN apt-get update \
    && curl -sL https://deb.nodesource.com/setup_4.x | bash - \
    && apt-get install -y --no-install-recommends \
      libicu-dev \
      libpq-dev \
      ca-certificates \
      ssl-cert \
      libcurl4-gnutls-dev \
      git \
      unzip \
      nodejs \
      mysql-client \
    && update-ca-certificates \
    && docker-php-ext-install \
      pdo_mysql \
      opcache \
      curl \
      zip \
    && apt-get autoremove \
    && rm -r /var/lib/apt/lists/*

WORKDIR /var/www

RUN a2enmod rewrite expires deflate
