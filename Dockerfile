FROM leejoneshane/laravel

RUN rm -rf /var/www/html /root/html

COPY html /var/www/

RUN composer require spatie/icalendar-generator && composer fund \
    && mkdir /root/html \
    && chown -R www-data:www-data /var/www /root/html \
    && cp -Rp /var/www/html/. /root/html
