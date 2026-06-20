FROM php:8.1-apache
COPY . /var/www/html/
RUN apt-get update && apt-get install -y libcurl4-openssl-dev && docker-php-ext-install curl && rm -rf /var/lib/apt/lists/*
RUN mkdir -p /var/www/html/files && chown -R www-data:www-data /var/www/html/files && chmod 755 /var/www/html/files
EXPOSE 80
