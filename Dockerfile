# ---------- Frontend ----------
FROM node:20 AS assets

WORKDIR /app

COPY package*.json ./
RUN npm install 

COPY . .
RUN npm run build

# ---------- Backend ----------
FROM dunglas/frankenphp:1-php8.3

WORKDIR /app

RUN install-php-extensions \
    pdo_pgsql \
    mbstring \
    intl \
    zip \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/{cache,sessions,views} \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 80

#CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
