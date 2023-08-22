#!/usr/bin/env bash

touch /var/log/nginx_access.log
touch /var/log/nginx_error.log
chmod 777 /var/log/nginx_access.log
chmod 777 /var/log/nginx_error.log
chmod -R 0777 /app/storage/logs

#composer install

service nginx start

cat /tmp/.env
supervisord -n