FROM php:7-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    libxslt1.1 libxslt1-dev \
    libicu-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd xsl intl zip pdo_mysql
RUN apt-get install -y libmemcached-dev unzip && \
    curl -o /root/memcached.zip https://github.com/php-memcached-dev/php-memcached/archive/php7.zip -L && \
    cd /root && unzip memcached.zip && rm memcached.zip && \
    cd php-memcached-php7 && \
    phpize && ./configure --enable-sasl && make && make install && \
    cd /root && rm -rf /root/php-memcached-* && \
    echo "extension=memcached.so" > /usr/local/etc/php/conf.d/memcached.ini  && \
    echo "memcached.use_sasl = 1" >> /usr/local/etc/php/conf.d/memcached.ini