# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.5.12
ARG NODE_VERSION=26.12.1
ARG COMPOSER_VERSION=2.11.1

# ---------------------------------------------------------------------------
# assets — builds public/build with Vite on the native build platform
# ---------------------------------------------------------------------------
FROM --platform=$BUILDPLATFORM node:${NODE_VERSION}-alpine AS assets

WORKDIR /app

ARG VITE_VUE_APP_I18N_LOCALE=id
ARG VITE_VUE_APP_I18N_FALLBACK_LOCALE=id
ENV VITE_VUE_APP_I18N_LOCALE=${VITE_VUE_APP_I18N_LOCALE} \
    VITE_VUE_APP_I18N_FALLBACK_LOCALE=${VITE_VUE_APP_I18N_FALLBACK_LOCALE}

COPY package.json package-lock.json vite.config.js ./
RUN npm ci --no-audit --no-fund

COPY resources ./resources
COPY public ./public
RUN npm run build

# ---------------------------------------------------------------------------
# composer_bin — the Composer phar copied into the runtime image.
# A phar is architecture independent, so this stage exists only to avoid
# variable expansion in `COPY --from` (unsupported by BuildKit).
# ---------------------------------------------------------------------------
FROM composer:${COMPOSER_VERSION} AS composer_bin

# ---------------------------------------------------------------------------
# vendor — Composer dependencies resolved for the platform PHP version
# ---------------------------------------------------------------------------
FROM --platform=$BUILDPLATFORM composer:${COMPOSER_VERSION} AS vendor

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --ignore-platform-req=ext-gd

COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes
COPY public ./public

RUN composer dump-autoload --no-dev --optimize --no-scripts

# ---------------------------------------------------------------------------
# app_base — PHP-FPM with the extensions this application needs
# ---------------------------------------------------------------------------
FROM php:${PHP_VERSION}-fpm-alpine AS app_base

ENV TZ=Asia/Jakarta \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache \
        bash \
        curl \
        git \
        su-exec \
        tzdata \
        libpng \
        libjpeg-turbo \
        freetype \
        libzip \
        icu-libs \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip bcmath intl pcntl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Zend OPcache ships compiled into the php:8.5 images — configure it, never build it.
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

COPY --from=composer_bin /usr/bin/composer /usr/bin/composer
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# ---------------------------------------------------------------------------
# dev — bind-mounted source, no baked code
# ---------------------------------------------------------------------------
FROM app_base AS dev

ENV APP_ENV=local \
    APP_DEBUG=true

RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---------------------------------------------------------------------------
# prod — application code, vendor/ and built assets baked in
# ---------------------------------------------------------------------------
FROM app_base AS prod

ENV APP_ENV=production \
    APP_DEBUG=false

COPY --chown=www-data:www-data . .

COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---------------------------------------------------------------------------
# web_prod — nginx serving exactly the assets from the same commit
# ---------------------------------------------------------------------------
FROM nginx:1.27-alpine AS web_prod

COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=prod /var/www/html/public /var/www/html/public
