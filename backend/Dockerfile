FROM php:8.2-apache

# Обновляем систему и устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    unzip zip curl git \
    libpq-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd

# Включаем Apache модуль mod_rewrite
RUN a2enmod rewrite

# Настройки Apache для .htaccess
RUN echo "<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>" >> /etc/apache2/apache2.conf

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем приложение
WORKDIR /var/www/html
COPY . .

# Копируем стартовый скрипт
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Открываем порт
EXPOSE 80

# Запуск проекта через скрипт
CMD ["/start.sh"]
