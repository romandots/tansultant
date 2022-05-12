FROM php:8.1.4-fpm-buster

EXPOSE 9009

RUN apt-get update -y \
    && apt-get install -y \
    curl \
    default-libmysqlclient-dev \
    git \
    iputils-ping \
    libcurl3-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libpq-dev \
    librabbitmq-dev \
    libxml2-dev \
    mc \
    nano \
    nginx \
    openssh-client \
    telnet \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pgsql \
    && docker-php-ext-install pdo_pgsql

ADD docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD docker/nginx.conf /etc/nginx/sites-enabled/default
ADD docker/entrypoint.sh /tmp/entrypoint.sh
RUN chmod 777 /tmp/entrypoint.sh

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php/php.ini \
    && sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php-fpm.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD ./ /app

WORKDIR /app

RUN composer install

ENTRYPOINT /tmp/entrypoint.sh