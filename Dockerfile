FROM leejoneshane/laravel

# what system type to compiler: intel cpu (below) or apple m1 (use 'armv7-pc-linux-musl')
ARG SYSTEM x86_64-pc-linux-musl

RUN rm -rf /var/www/html /root/html

COPY html /var/www/

RUN composer require spatie/icalendar-generator \
    && chown -R www-data:www-data /var/www \
    && cp -Rp /var/www/html /root
