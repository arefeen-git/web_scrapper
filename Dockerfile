FROM php:fpm

RUN apt-get update && apt-get install -y \
    zlib1g-dev g++ git libicu-dev zip libzip-dev zip librabbitmq-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/html/holy_scrap

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN composer require symfony/orm-pack
RUN composer require --dev symfony/maker-bundle
RUN composer require symfony/messenger
RUN composer require symfony/amqp-messenger
