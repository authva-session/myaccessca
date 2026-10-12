dockerfile
# Use an official PHP image with Apache pre-installed
FROM php:8.2-apache

# Install PDO and MySQL extensions so your PHP code can talk to the database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite if needed for redirects
RUN a2enmod rewrite

# Copy your local code files into the web server directory
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80
