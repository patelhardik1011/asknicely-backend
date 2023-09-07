FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y && apt-get install -y --no-install-recommends \
    libc-dev \
    libc-client-dev \
    libc-client2007e \
    file \
    g++ \
    gcc \
    make \
    autoconf \
    automake \
    pkg-config re2c \
    libkrb5-dev \
    libxml2-dev \
    libbz2-dev \
    build-essential \
    libssl-dev \
    openssl \
    gnupg \
    gnupg2 \
    ca-certificates \
    bash \
    bash-completion \
    gettext \
    curl \
    wget \
    less \
    gawk \
    grep \
    sed \
    procps \
    psmisc \
    git \
    mc \
    nano \
    zip \
    unzip \
    tar \
    gzip \
    bzip2 \
    zlib1g \
    zlib1g-dev \
    libicu-dev \
    libtidy-dev \
    libmemcached11 \
    libmemcachedutil2 \
    libmemcached-dev \
    libgearman-dev \
    libsodium-dev \
    libfreetype6 \
    libfreetype6-dev \
    libpng16-16 \
    libpng-dev \
    libjpeg62-turbo \
    libjpeg62-turbo-dev \
    libfontconfig1 \
    libxrender1 \
    libxext6 \
    fonts-wqy-microhei \
    ttf-wqy-microhei \
    fonts-wqy-zenhei \
    ttf-wqy-zenhei \
    apache2-dev \
    redis-tools \
    libonig-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN set -xe \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && PHP_OPENSSL=yes docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap \
    && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    iconv \
    xmlrpc \
    xml \
    soap \
    intl \
    sodium \
    gettext \
    mbstring \
    tidy \
    opcache \
    bz2

RUN apt-get update \
  && apt-get install -y autoconf build-essential \
  && apt-get install -y libzip-dev \
  && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create system user to run Composer and Artisan Commands
#ADD . /var/www
#RUN chown -R www-data:www-data /var/www
#RUN chmod 755 /var/www

# Change current user to www
#USER root