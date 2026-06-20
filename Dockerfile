FROM php:8.1-apache
COPY . /var/www/html/
RUN docker-php-ext-install curl json
RUN a2enmod rewrite
EXPOSE 80
