FROM php:8.2-cli

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /app

# Copy all files
COPY . .

# Expose port
EXPOSE 8080

# Start PHP server
CMD php -S 0.0.0.0:$PORT -t public/
