# Etapa I: Construcción de archivos
FROM debian AS builder

# Instalar Node.js y herramientas de construcción
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Establecer el directorio de trabajo
WORKDIR /var/www

# Copiar archivos de la aplicación
COPY . /var/www

# Instalar dependencias de Node.js y construir archivos de producción
RUN npm install && npm run build

# Etapa II: Imagen de producción de Nginx
FROM nginx:alpine

# Copiar configuración personalizada de Nginx
COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

# Copiar archivos públicos de Laravel desde la etapa de construcción
COPY --from=builder /var/www/public /var/www/public

# Establecer el directorio de trabajo
WORKDIR /var/www/public

# Exponer el puerto 80
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]