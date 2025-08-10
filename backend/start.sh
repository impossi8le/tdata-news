#!/bin/bash

until nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 3
done

echo "Зависимости ..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "Выполняем миграции..."
php artisan migrate --path=plugins/tdata/news/updates
php artisan october:migrate

echo "Чистка..."
php artisan optimize:clear

echo "Добавление root..."
php artisan db:seed --class="TData\News\Updates\SeedTDataUser"

echo "Запускаем сервер"
php artisan serve --host=0.0.0.0 --port=80
