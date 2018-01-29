FROM php:7.0-cli

ENV TERM xterm

# Dev tools
RUN apt-get update && apt-get install -y mc nano git sudo supervisor && rm -r /var/lib/apt/lists/*

# Composer
RUN curl -o ./composer-setup.php https://getcomposer.org/installer \
    && php composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer rm -rf /tmp/composer-setup.php \
    && rm -rf composer-setup.php

# gosu
RUN curl -o /usr/local/bin/gosu -SL "https://github.com/tianon/gosu/releases/download/1.5/gosu-$(dpkg --print-architecture)" \
   && curl -o /usr/local/bin/gosu.asc -SL "https://github.com/tianon/gosu/releases/download/1.5/gosu-$(dpkg --print-architecture).asc" \
   && rm /usr/local/bin/gosu.asc \
   && chmod +x /usr/local/bin/gosu

###
# Final configuration
###

# Configs
COPY conf/php.ini $PHP_INI_DIR/
COPY conf/supervisord.conf /etc/supervisor/conf.d/

# Add system group/user
RUN groupadd -r docker && useradd -r -g docker -s /bin/bash -m docker

# USER docker
WORKDIR /var/www

COPY entrypoint.sh /

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["/usr/bin/supervisord"]