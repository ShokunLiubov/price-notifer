FROM php:8.3-fpm-alpine3.19

#Libs
RUN apk add --no-cache curl curl-dev dcron bash

#Additional soft
RUN apk add vim bash

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-install curl mysqli pdo pdo_mysql

#Install php extensions
RUN docker-php-ext-install curl
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

#Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Add crontab file in the cron directory
COPY crontab /etc/crontabs/root

# Grant execution rights on the cron job
RUN chmod 0644 /etc/crontabs/root

# Apply cron job
RUN crontab /etc/crontabs/root

# Create log file to be able to run tail
RUN touch /var/log/cron.log
RUN mkdir -p /var/log/sql.log

CMD ["sh", "-c", "crond && php-fpm"]

WORKDIR /var/www/

EXPOSE 9000
