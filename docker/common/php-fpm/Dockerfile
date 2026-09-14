# Etapa I: Crear entorno y dependencias de Composer
FROM php:8.5-fpm AS builder

# Instalar dependencias del sistema y extensiones de PHP para Laravel con soporte para MySQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libpq-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    intl \
    zip \
    bcmath \
    soap \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Declarar el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Copiar los archivos de la aplicación al contenedor
COPY . /var/www

# Instalar Composer y sus dependencias

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# Etapa II: Entorno de producción
FROM php:8.5-fpm AS production

# Instalar solamente las librerias necesarias en producción
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfcgi-bin \
    procps \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Descargar e instalar el script de chequeo de salud de PHP-FPM
RUN curl -o /usr/local/bin/php-fpm-healthcheck https://raw.githubusercontent.com/docker-library/healthcheck/master/php/fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

# Copiar el script de inicialización
COPY ./docker/production/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copiar la estructura de almacenamiento inicial
COPY ./storage /var/www/storage-init

# Copiar extensiones y configuraciones de PHP de la etapa de construcción
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

# Usar la configuración de producción recomendada de PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Mantener la configuración global de PHP-FPM
COPY ./docker/common/php-fpm/conf.d/*.conf /usr/local/etc/php-fpm.d/

# Copiar la aplicación desde la etapa de construcción
COPY --from=builder /var/www /var/www

# Establecer el directorio de trabajo
WORKDIR /var/www

# Crear usuario con los permisos correctos para el directorio de trabajo
RUN chown -R www-data:www-data /var/www

# Cambiar al usuario no privilegiado creado anteriormente
USER www-data

# Cambiar el comando por defecto para iniciar el script de inicialización
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Exponer el puerto 9000 para PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]

# Etapa III: Construir capa de desarrollo encima de la capa de producción
FROM production AS development

# Usar ARGs para definir variables de entorno pasados desde Docker
ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER
ARG XDEBUG_LOG=/dev/stdout
ARG XDEBUG_LOG_LEVEL=0

USER root

# Configurar Xdebug si está habilitado
RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=${XDEBUG_LOG}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=${XDEBUG_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ; \
fi

# Añadir ARGs para sincronizar permisos
ARG UID=1000
ARG GID=1000

# Crear un nuevo usuario con el UID y GID especificados, reusando un grupo existente si el GID ya existe
RUN if getent group ${GID}; then \
        group_name=$(getent group ${GID} | cut -d: -f1); \
        useradd -m -u ${UID} -g ${GID} -s /bin/bash www; \
    else \
        groupadd -g ${GID} www && \
        useradd -m -u ${UID} -g www -s /bin/bash www; \
        group_name=www; \
    fi

# Actualizar dinamicamente php-fpm para usar el nuevo usuario y grupo
RUN sed -i "s/user = www-data/user = www/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/group = www-data/group = $group_name/g" /usr/local/etc/php-fpm.d/www.conf

# Establecer el directorio de trabajo
WORKDIR /var/www

# Copiar el script de inicialización
COPY ./docker/development/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Cambiar al usuario no privilegiado
USER www-data

# Cambiar el comando por defecto para iniciar el script de inicialización
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Exponer el puerto 9000 para PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]