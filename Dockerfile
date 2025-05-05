FROM php:8.2-apache

# Instalar extensiones necesarias si usás base de datos
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aumentar tamaño máximo de archivos subidos
RUN echo "upload_max_filesize=500M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=500M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copiar tus archivos al contenedor
COPY . /var/www/html/

# Asignar permisos (si necesitás escribir archivos como videos subidos)
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80
