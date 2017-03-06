FROM 4xxi/php-pgsql

COPY . /var/www/server
WORKDIR /var/www/server

RUN composer install --prefer-dist
CMD /var/www/server/composer.sh
