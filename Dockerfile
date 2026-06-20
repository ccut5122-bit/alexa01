FROM php:8.1-apache
COPY . /var/www/html/
RUN apt-get update && apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install curl
EXPOSE 80
