FROM php:8.4-fpm

EXPOSE 9009
EXPOSE 6001

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
    curl \
    default-libmysqlclient-dev \
    git \
    iputils-ping \
    libcurl4-openssl-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
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
    supervisor \
    htop \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && docker-php-ext-install -j$(nproc) \
       pgsql pdo_pgsql exif pdo_mysql mysqli pcntl sockets

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker/nginx.conf /etc/nginx/sites-enabled/default
COPY docker/entrypoint.sh /tmp/entrypoint.sh

RUN chmod 755 /tmp/entrypoint.sh \
    && mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

ENV PATH="/usr/local/bin:$PATH"
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY ./src/composer.json ./src/composer.lock ./

ARG COMPOSER_IGNORE_PLATFORM_REQS=1
ENV COMPOSER_IGNORE_PLATFORM_REQS=${COMPOSER_IGNORE_PLATFORM_REQS}
RUN composer install --no-interaction --no-progress --no-autoloader --no-dev --no-scripts

COPY ./src .

RUN composer install --no-interaction --no-progress --optimize-autoloader

COPY .git /.git
RUN git config --global --add safe.directory /app \
    && echo "export GIT_LAST_COMMIT=$(git log -1 --format="%at" | TZ=Europe/Moscow xargs -I{} date -d @{} +%Y%m%d.%H%M)" >> /etc/bash.bashrc

ENTRYPOINT ["/tmp/entrypoint.sh"]
