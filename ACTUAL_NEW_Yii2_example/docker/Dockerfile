ARG ENV=prod

FROM php:7.3-fpm-alpine3.8 AS base

ARG YII_ENV
RUN echo "YII_ENV: $YII_ENV"

# либы и экстеншены php
RUN apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS
RUN apk add --no-cache --update --virtual .imagick-build-dependencies imagemagick-dev
RUN apk add --no-cache --update --virtual .memcached-deps zlib-dev libmemcached-dev cyrus-sasl-dev
RUN apk add --no-cache --update --virtual .imagick-runtime-deps imagemagick

RUN apk --no-cache --update add \
    composer \
    nodejs \
    nodejs-npm \
    memcached \
    redis \
    g++ \
    util-linux \
    shadow \
    aspell-dev \
    autoconf \
    build-base \
    postgresql-dev \
    openssh-client \
    supervisor \
    linux-headers \
    libc6-compat \
    libaio-dev \
    libpng-dev \
    zlib-dev \
    libmemcached-libs \
    zlib \
    libzip-dev \
    icu-dev \
    curl \
    git \
    subversion \
    freetype-dev \
    libjpeg-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libtool \
    libbz2 \
    bzip2-dev \
    libxslt-dev \
    openldap-dev \
    libltdl \
    hiredis-dev \
    make \
    unzip \
    ffmpeg \
    wget && \
    pecl install imagick && \
    docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-configure intl && \
    docker-php-ext-install zip intl && \
    docker-php-ext-install bcmath bz2 pdo_pgsql pgsql simplexml opcache sockets mbstring pcntl xsl pspell && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-enable imagick intl && \
    docker-php-ext-install gd && \
    docker-php-ext-enable opcache

RUN export HOSTNAME=$(uuidgen)

# memcached
ENV MEMCACHED_MEMORY 128
ENV MEMCACHED_MAX_CONNECTIONS 1024
ENV MEMCACHED_MAX_ITEM_SIZE 4M
RUN set -xe && \
    cd /tmp/ && \
    # Install igbinary (memcached's deps)
    pecl install igbinary && \
    # Install memcached
    ( \
        pecl install --nobuild memcached && \
        cd "$(pecl config-get temp_dir)/memcached" && \
        phpize && \
        ./configure --enable-memcached-igbinary && \
        make -j$(nproc) && \
        make install && \
        cd /tmp/ \
    ) && \
    # Enable PHP extension
    docker-php-ext-enable igbinary memcached && \
    rm -rf /tmp/*

#redis
COPY redis /etc/redis/conf.d

#LDAP
RUN docker-php-ext-install ldap
RUN docker-php-ext-enable ldap

#AMQP
RUN apk add rabbitmq-c rabbitmq-c-dev
RUN pecl install amqp
RUN docker-php-ext-enable amqp

# Удалить временные либы
RUN  apk del .memcached-deps \
        .phpize-deps \
        .imagick-build-dependencies \
        linux-headers \
        libaio-dev && \
        rm -rf /var/cache/apk/*


#Устанавливаем NGINX
RUN mkdir /hosts
RUN apk add nginx
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/common.conf /etc/nginx/common.conf
COPY hosts /hosts

# Гитхаб в доверенные хосты
RUN apk add git
RUN mkdir ~/.ssh
RUN ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

# Ключ гитхаба для композера
COPY php/composer/auth.json /root/.composer/auth.json

COPY services /services
RUN chmod +x /services/websocket.sh
RUN chmod +x /services/parsing-consumer.sh

# Копируем настройки пыхи
COPY php/ini/custom.ini /usr/local/etc/php/conf.d/php.ini
COPY php/conf/fpm.conf /usr/local/etc/pool.d/microservice-fpm.conf
COPY php/conf/fpm.conf /usr/local/etc/php-fpm.d/custom.conf
RUN cp /etc/php7/php.ini /usr/local/etc/php/php.ini

COPY supervisor/services.ini /etc/supervisor.d/services.ini

ENV TZ=Europe/Moscow
RUN apk --update add tzdata && ln -sf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone


EXPOSE 80
EXPOSE 443
EXPOSE 9000
EXPOSE 8891
EXPOSE 8892

# Указываем рабочую папку
WORKDIR /app

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

FROM base AS prod
COPY cron/crontab /tmp/crontab
RUN crontab /tmp/crontab

FROM base AS dev
COPY cron/crontab.dev /tmp/crontab
RUN crontab /tmp/crontab

FROM $ENV