FROM epcallan/php7-testing-phpunit:7.2-phpunit7

COPY config/composer.json /composer.json
RUN composer install --no-autoloader

CMD composer dumpautoload && phpunit --bootstrap vendor/autoload.php tests
