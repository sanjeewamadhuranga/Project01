FROM php:8.1-apache

RUN apt-get update && apt-get install --yes --no-install-recommends \
    libssl-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    ;

# Install wkhtmltopdf with patched QT
RUN apt-get install --yes --no-install-recommends \
    fontconfig \
    libxrender1 \
    libxext6 \
    xfonts-75dpi \
    xfonts-base \
    && curl -sL \
    "https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_$(dpkg-architecture -q DEB_BUILD_ARCH).deb" \
    -o wkhtmltopdf.deb \
    && dpkg -i wkhtmltopdf.deb \
    && rm wkhtmltopdf.deb

RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl \
        gd \
        zip

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb opcache

RUN a2enmod headers

# Copy configuration files
COPY .docker/apache/http.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/php.ini /usr/local/etc/php/conf.d

# Copy application contents
COPY . /var/www/html

# Always download latest version of Composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run runtime dependency check to see if we don't miss any extension
RUN composer check

ENV APP_ENV=prod
ENV WKHTMLTOPDF_PATH=/usr/local/bin/wkhtmltopdf

#RUN bin/console c:c

#RUN chown -R www-data:www-data var
