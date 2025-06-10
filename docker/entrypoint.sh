#!/usr/bin/env bash

cd /app
yes | php artisan migrate
yes | php artisan db:seed
chmod -R 0777 /app/storage/

/usr/bin/supervisord -n