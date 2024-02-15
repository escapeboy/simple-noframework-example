FROM php:8.3-cli
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Install Redis with PECL and enable the extension
RUN pecl install redis
RUN docker-php-ext-enable redis

RUN apt-get update \
     && docker-php-ext-install mysqli pdo pdo_mysql \
     && docker-php-ext-enable pdo_mysql \
     && apt-get -y install git zip
WORKDIR /app