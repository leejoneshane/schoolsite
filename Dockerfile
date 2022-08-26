FROM leejoneshane/laravel

COPY html /var/www/

RUN composer require spatie/icalendar-generator && composer fund \
    && chown -R www-data:www-data /var/www \
    && cp -Rp /var/www/html/. /root/html
