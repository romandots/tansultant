#!/usr/bin/env bash

touch /var/log/nginx_access.log
touch /var/log/nginx_error.log
chmod 777 /var/log/nginx_access.log
chmod 777 /var/log/nginx_error.log

service nginx start
php-fpm