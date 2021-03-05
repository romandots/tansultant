FROM php:7.4-fpm

RUN apt-get update -y \
    && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    iputils-ping \
    telnet \
    openssh-client \
    git \
    mc \
    unzip \
    librabbitmq-dev \
    default-libmysqlclient-dev \
    libicu-dev \
    libpq-dev \
    curl \
    libcurl3-dev \
    libxml2-dev \
    nginx \
    nano

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get install -y libpq-dev \
  && docker-php-ext-install pgsql \
  && docker-php-ext-install pdo_pgsql

RUN pecl install redis && docker-php-ext-enable redis

ADD ./docker/entrypoint.sh /tmp/entrypoint.sh
RUN chmod 777 /tmp/entrypoint.sh

ADD ./docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD ./docker/nginx.conf /etc/nginx/sites-enabled/default

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php/php.ini
RUN sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php-fpm.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

EXPOSE 9009

ENTRYPOINT /tmp/entrypoint.sh