FROM php:8.3-cli

RUN apt-get update --fix-missing
RUN apt-get update && apt-get install -y \
    curl \
    gettext \
    git \
    openssl \
    openssh-client \
    zip \
    net-tools \
    nano \
    build-essential \
    libssl-dev \
    zlib1g-dev \
    libpng-dev \
    libpq-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    postgresql-client \
    libxml2-dev \
    libicu-dev \
    libxslt-dev \
    xsltproc

RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/

RUN docker-php-ext-install bcmath exif gd gettext pdo pdo_mysql mysqli zip opcache soap simplexml pcntl intl xsl

RUN pecl install apcu && docker-php-ext-enable apcu

RUN pecl install xdebug

#####################
#                   #
#   Setup Composer  #
#                   #
#####################

WORKDIR /tmp

ENV COMPOSER_HOME /composer

# Add global binary directory to PATH and make sure to re-export it
ENV PATH /composer/vendor/bin:$PATH

# Allow Composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Setup the Composer installer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }"

RUN php /tmp/composer-setup.php

RUN mv /tmp/composer.phar /usr/local/bin/composer.phar && \
    ln -s /usr/local/bin/composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

#################
#               #
#   Configure   #
#               #
#################

ARG SYSTEM_TIMEZONE=UTC

RUN ln -snf /usr/share/zoneinfo/${SYSTEM_TIMEZONE} /etc/localtime && echo ${SYSTEM_TIMEZONE} > /etc/timezone
ENV TZ="${SYSTEM_TIMEZONE}"

ARG APP_ENV=dev
ARG PHP_XDEBUG_ENABLE=1
ARG PHP_XDEBUG_MODE=debug
ARG PHP_XDEBUG_REMOTE_PORT=9003
ARG PHP_XDEBUG_REMOTE_ADDRESS=host.docker.internal
ARG PHP_XDEBUG_IDEKEY=PHPSTORM
ARG PHP_IDE_CONFIG="serverName=localhost"

ENV PHP_XDEBUG_ENABLE=${PHP_XDEBUG_ENABLE}
ENV PHP_XDEBUG_MODE=${PHP_XDEBUG_MODE}
ENV PHP_XDEBUG_REMOTE_PORT=${PHP_XDEBUG_REMOTE_PORT}
ENV PHP_XDEBUG_REMOTE_ADDRESS=${PHP_XDEBUG_REMOTE_ADDRESS}
ENV PHP_XDEBUG_IDEKEY=${PHP_XDEBUG_IDEKEY}
ENV PHP_IDE_CONFIG=${PHP_IDE_CONFIG}
ENV SYSTEM_TIMEZONE=${SYSTEM_TIMEZONE}

ADD docker/php/conf /tmp
RUN chmod +x /tmp/configure.sh
RUN /tmp/configure.sh

#################################
#                               #
#   Add GitHub to known hosts   #
#                               #
#################################

RUN cd; mkdir .ssh; chmod 0700 .ssh; touch /root/.ssh/known_hosts
RUN ssh-keyscan github.com >> /root/.ssh/known_hosts

WORKDIR /www
COPY --chown=${LOCAL_USER} . /www

#################################
#                               #
#   Holds a raised container    #
#                               #
#################################

COPY docker/php/cmd.sh /usr/local/bin/cmd.sh
RUN chmod +x /usr/local/bin/cmd.sh

CMD ["/bin/bash", "-c", "/usr/local/bin/cmd.sh"]