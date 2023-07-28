FROM php:8.1-fpm
ARG USERID=1000
ARG USERNAME=laravel

#installing composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y \
    libfreetype6-dev libpq-dev libwebp-dev libgd-dev \
    libzip4 libzip-dev\
    libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp &&\
    docker-php-ext-install gd pdo_pgsql pgsql zip exif

RUN useradd -m -u ${USERID} ${USERNAME}

WORKDIR /app
COPY . /app

RUN chown -R www-data:www-data /app
RUN echo "upload_max_filesize = 100M \n post_max_size = 100M" >> /usr/local/etc/php/conf.d/docker-fpm.ini
EXPOSE 9000
USER ${USERNAME}
