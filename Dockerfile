FROM php:8.2-apache

# Install required extensions
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && a2enmod rewrite

# Copy PHP files
COPY index.php /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 10000

# Update Apache to listen on PORT from environment
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
