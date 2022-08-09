ARG IMAGE_NAME=php

FROM ${IMAGE_NAME}:8.1.0-fpm as base

RUN apt-get update && \
  apt-get install -y nodejs npm zlib1g-dev libpq-dev libxml2-dev git unzip libgmp-dev re2c libmhash-dev libmcrypt-dev file libfcgi-bin \
    libc-client-dev libkrb5-dev locales gettext vim nano openssl python3 python3-pip python3-venv \
    librdkafka-dev \
    libonig-dev libzip-dev && \
    pecl install apcu pcov rdkafka-5.0.2 && \
    docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-configure pdo_pgsql --with-pdo-pgsql && \
    docker-php-ext-configure mbstring --enable-mbstring && \
    docker-php-ext-configure gettext && \
    docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install -j$(nproc) \
     gettext \
     iconv \
     intl \
     mbstring \
     opcache \
     pcntl \
     pdo_pgsql \
     sockets \
     zip && \
  docker-php-ext-enable apcu rdkafka

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Composer
RUN curl -sS https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- \
     --version=2.2.7 \
     --filename=composer \
     --install-dir=/usr/local/bin && \
  mkdir -m 777 -p /var/composer/ && \
  chmod -R 777 /var

ENV COMPOSER_CACHE_DIR /var/composer/

# Locale
RUN echo "ru_RU.UTF-8 UTF-8" >> /etc/locale.gen \
    echo "ru_RU.CP1251 CP1251" >> /etc/locale.gen && \
    locale-gen
ENV LANG C.UTF-8
ENV LC_ALL C.UTF-8

ADD php.ini  /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]

FROM base as local

ADD php.local.ini /usr/local/etc/php/conf.d/custom-2.ini

# Change www-data user to local id
RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data

USER 1000
