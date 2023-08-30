FROM devilbox/php-fpm:8.2-work

# Copy your application code to the container
COPY --chown=devilbox:devilbox [^docker]* /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000
