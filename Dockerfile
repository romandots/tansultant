FROM php:8.3-fpm

EXPOSE 9009
EXPOSE 6001

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
    supervisor \
    htop \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pgsql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install exif \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install sockets

ADD docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD docker/nginx.conf /etc/nginx/sites-enabled/default
ADD docker/entrypoint.sh /tmp/entrypoint.sh
ADD docker/supervisor.conf /etc/supervisor/conf.d
RUN chmod 777 /tmp/entrypoint.sh

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
#    && sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php/php.ini \
#    && sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php-fpm.conf \
#    && sed -i 's|;error_log = .*|error_log = /proc/self/fd/2|' /usr/local/etc/php-fpm.d/www.conf
#    && echo "pm.max_children = 20" >> /usr/local/etc/php-fpm.d/www.conf

ADD ./src /app
WORKDIR /app

# Install Composer and dependencies
ENV PATH="/usr/local/bin:$PATH"
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Disable platform requirements for Tochka Bank SDK
ARG COMPOSER_IGNORE_PLATFORM_REQS
ENV COMPOSER_IGNORE_PLATFORM_REQS=1
RUN composer install

# Export last git commit as patch version
COPY .git /.git
RUN git config --global --add safe.directory /app
RUN bash -l -c 'echo $(git log -1 --format="%at" | TZ=Europe/Moscow xargs -I{} date -d @{} +%Y%m%d.%H%M) >> /git_last_commit'
RUN echo "export GIT_LAST_COMMIT=$(cat /git_last_commit)" >> /etc/bash.bashrc

ENTRYPOINT /tmp/entrypoint.sh