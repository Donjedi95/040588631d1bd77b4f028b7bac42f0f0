FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN apk --update --no-cache add git
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; php-fpm; php bin/app app:import:mindgeek:images
EXPOSE 9000