FROM php:7.1-fpm-stretch

MAINTAINER Descamps Antoine <antoine.descamps@ineat-conseil.fr>

RUN apt-get clean && apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
        libpq-dev \
        libxpm-dev \
        libvpx-dev \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install -j$(nproc) mcrypt \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-configure gd \
		--enable-gd-native-ttf \
		--with-freetype \
		--with-jpeg \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
		--with-png-dir=/usr/include/ \
        --with-xpm-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-vpx-dir=/usr/lib/x86_64-linux-gnu/ \
	&& docker-php-ext-enable gd
	
RUN apt-get install -q -y ssmtp mailutils

RUN echo "FromLineOverride=YES" >> /etc/ssmtp/ssmtp.conf
RUN echo "mailhub=smtp.mail.ru:465" >> /etc/ssmtp/ssmtp.conf
RUN echo "AuthUser=noreply@vrnexthome.com" >> /etc/ssmtp/ssmtp.conf
RUN echo "AuthPass=RiaR-pUstO16" >> /etc/ssmtp/ssmtp.conf
RUN echo "UseTLS=YES" >> /etc/ssmtp/ssmtp.conf

RUN apt-get install -y mc