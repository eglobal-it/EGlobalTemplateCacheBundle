FROM php:7.0-cli

# Dev tools
RUN apt-get update && apt-get install -y git zip unzip && rm -r /var/lib/apt/lists/*

# Composer
RUN curl -o ./composer-setup.php https://getcomposer.org/installer \
    && php composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --version=1.5.1 \
    && rm -rf composer-setup.php

# Deploy project
ARG BRANCH
RUN git clone -b $BRANCH https://github.com/eglobal-it/EGlobalTemplateCacheBundle.git /var/www \
    && cd /var/www \
    && composer install

# Tests
RUN cd /var/www \
    && ./vendor/bin/php-cs-fixer fix --dry-run -v --using-cache=null src \
    && ./vendor/bin/phpunit tests