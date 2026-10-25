# Imagen para desplegar la demo en Render (PHP 8.4 + build del front con Node)
FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip curl ca-certificates libzip-dev \
    && docker-php-ext-install zip bcmath pcntl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer (versión fija, descargada de su release oficial en GitHub)
RUN curl -fsSL -o /usr/local/bin/composer https://github.com/composer/composer/releases/download/2.8.12/composer.phar \
    && chmod +x /usr/local/bin/composer

WORKDIR /app

# 1) Dependencias PHP y JS (capas cacheables)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci

# 2) Código y compilación del front (Vite necesita PHP para generar rutas Wayfinder/Ziggy)
COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && VITE_APP_NAME="Sistema de Matriculados" APP_KEY="base64:$(head -c 32 /dev/urandom | base64)" npm run build \
    && rm -rf node_modules

ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr \
    PHP_CLI_SERVER_WORKERS=4

EXPOSE 10000
CMD ["sh", "docker/start.sh"]
