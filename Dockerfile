FROM php:8.0-fpm-alpine as symfony-starter-base
ARG APP_ENV
ARG BASE_DIR
ARG PROJECT_USER_ID=1000
ARG PROJECT_GROUP_ID=1000
ARG IS_LOCAL=false
ARG XDEBUG_LOG
ARG YARN_CACHE_DIR
ENV APP_ENV=$APP_ENV
ENV BASE_DIR=$BASE_DIR
ENV IS_LOCAL=$IS_LOCAL
ENV YARN_CACHE_DIR=$YARN_CACHE_DIR

RUN set -ex ; \
    \
    # Create app user
    existing_group=$(getent group "${PROJECT_GROUP_ID}" | cut -d: -f1) ; \
    if [[ -n "${existing_group}" ]] ; then delgroup "${existing_group}" ; fi ; \
    existing_user=$(getent passwd "${PROJECT_USER_ID}" | cut -d: -f1) ; \
    if [[ -n "${existing_user}" ]] ; then deluser "${existing_user}" ; fi ; \
    \
        addgroup -g "${PROJECT_GROUP_ID}" -S app ; \
        adduser -u "${PROJECT_USER_ID}" -D -S -s /bin/bash -G app app ; \
        adduser app www-data ; \
        sed -i '/^app/s/!/*/' /etc/shadow ; \
    \
    # Build Dependencies
    apk add --no-cache --update --virtual buildDeps \
        autoconf \
        g++ \
        make ; \
    # Install packages
    apk add --no-cache --update \
        bash \
        curl \
        git \
        glib \
        icu-dev \
        libgcc \
        libstdc++ \
        libintl \
        libpng-dev \
        libx11 \
        libxext \
        libxrender \
        nano \
        nginx \
        nginx-mod-http-headers-more \
        npm \
        runit \
        screen \
        sudo \
        ttf-dejavu \
        ttf-droid \
        ttf-freefont \
        ttf-liberation  \
        zstd-dev ; \
    # Install PHP packages
    docker-php-ext-install intl pdo_mysql ; \
    yes | pecl install -o -f igbinary ; \
    docker-php-ext-enable intl pdo_mysql sodium ; \
    \
    # Install local dependencies
    if [[ "${IS_LOCAL}" == true ]]; then \
        pecl install xdebug ; \
        touch ${XDEBUG_LOG} ; \
        chown app:www-data ${XDEBUG_LOG} ; \
    fi ; \
    \
    # Install yarn
    npm install --global yarn ; \
    \
    # Configure environment
    install -o app -g www-data -d ${BASE_DIR} ; \
    install -o nginx -g nginx -d /run/nginx ; \
    chown -R nginx:nginx /etc/nginx ; \
    { \
        if [[ "${IS_LOCAL}" == true ]] ; then \
            echo 'app ALL=(root) NOPASSWD:SETENV:ALL' ; \
        else \
            echo -n 'app ALL=(root) NOPASSWD:SETENV: ' ; \
            echo -n ""${BASE_DIR}", " ; \
            echo -n '/tmp, ' ; \
            echo -n '/sbin/runit-wrapper, ' ; \
            echo -n '/usr/local/sbin/php-fpm, ' ; \
            echo -n '/usr/sbin/nginx , ' ; \
            echo -n '/usr/sbin/sshd, ' ; \
            echo '/usr/sbin/crond' ; \
        fi ; \
    } | tee /etc/sudoers.d/app ; \
    \
    # Clean up
    docker-php-source delete ; \
    apk del buildDeps ; \
    rm -rf /tmp/* /var/cache/* /etc/nginx/conf.d /etc/nginx/http.d /etc/nginx/sites-available ;

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY ./docker/app/config/php/php.${APP_ENV}.ini /usr/local/etc/php/php.ini
COPY ./docker/app/config/php/conf.d/ /usr/local/etc/php/conf.d/
COPY ./docker/app/config/php-fpm.d/ /usr/local/etc/php-fpm.d/
COPY ./docker/app/config/nginx/ /etc/nginx/
COPY ./docker/app/bin/ /usr/local/bin/
COPY ./docker/app/sbin/ /sbin/
COPY ./docker/app/etc/ /etc/
COPY --chown=app:www-data . ${BASE_DIR}

USER app
WORKDIR ${BASE_DIR}


##
# Production Environment
# ----------------------
FROM symfony-starter-base as symfony-starter-prod

CMD composer-install ; \
    bin/console assets:install ; \
    yarn-install ; \
    bin/console doctrine:migrations:migrate -n ; \
    bin/console cache:warm ; \
    sudo /sbin/runit-wrapper

EXPOSE 8080


##
# Remote Staging Environment
# --------------------------
FROM symfony-starter-prod as symfony-starter-stage


##
# Remote Development Environment
# ------------------------------
FROM symfony-starter-prod as symfony-starter-dev


##
# Remote Testing Environment
# --------------------------
FROM symfony-starter-dev as symfony-starter-test


##
# Local Environment
# -----------------
FROM symfony-starter-base as symfony-starter-local
ARG DATABASE_HOST
ARG DATABASE_PORT

CMD composer-install ; \
    #bin/console assets:install ; \
    #yarn-install ; \
    wait-for-it ${DATABASE_HOST}:${DATABASE_PORT} -- bin/console doctrine:migrations:migrate -n --no-debug ; \
    sudo /sbin/runit-wrapper

EXPOSE 8080 9000
