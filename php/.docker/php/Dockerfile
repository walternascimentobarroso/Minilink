# Image and version
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && \
    apt-get install --no-install-recommends -y \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    git \
    libicu-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    wget \
    libpq-dev \
    libxml2-dev \
    libssl-dev \
    librabbitmq-dev \
    libxslt1-dev

# Configure GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Set environment variables for libxslt
ENV XSL_CFLAGS=-I/usr/include/libxml2
ENV XSL_LIBS=-lxslt

# Call PHP images script `docker-php-ext-install` and install language extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    gd \
    intl \
    opcache \
    bcmath \
    xsl \
    soap \
    sockets

# Enable PHP extensions with `docker-php-ext-enable`
RUN docker-php-ext-enable \
    pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP_CodeSniffer globally
RUN composer global require "squizlabs/php_codesniffer=*"

# Install PHPStan globally
RUN composer global require "phpstan/phpstan"

# Add Composer global bin to the PATH
ENV PATH="$PATH:/root/.composer/vendor/bin"

# Install xdebug (specific version)
RUN pecl install xdebug-3.2.2 && docker-php-ext-enable xdebug

# Install amqp (specific version)
RUN pecl install amqp-1.11.0 && docker-php-ext-enable amqp

# Install mongodb
RUN pecl install mongodb-1.16.2 && docker-php-ext-enable mongodb

# Install apcu
RUN pecl install apcu && docker-php-ext-enable apcu

# Copy configuration files
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY overrides.ini /usr/local/etc/php/conf.d/overrides.ini

# Install Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Clean the image
RUN apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
