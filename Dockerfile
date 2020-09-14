FROM php:7.4.10-cli-alpine3.12 as prod

RUN set -ex \
  && apk add --no-cache --update --virtual buildDeps gcc g++ make autoconf curl-dev openssl-dev \
  && pecl install redis \
  && pecl install mongodb \
  && docker-php-ext-enable redis \
  && docker-php-ext-enable mongodb \
  && pecl config-set php_ini /etc/php.ini \
  && apk del buildDeps

FROM prod AS dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN apk add --no-cache --update --virtual buildDeps gcc g++ make autoconf \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

WORKDIR /code

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/code/public/"]
