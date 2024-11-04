#!/bin/bash

# If the vendor directory does not exist or is empty, install dependencies
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor)" ]; then
    echo "######################################"
    echo "Installing Composer dependencies..."
    echo "######################################"
    composer install --prefer-dist --no-scripts --no-interaction
    mkdir -p ./var
    chmod -R a+rwx ./vendor ./var
else
    echo "Composer vendor directory already exists and is not empty."
fi

if [ ! -d "config/jwt" ] || [ -z "$(ls -A config/jwt)" ]; then
    echo "######################################"
    echo "Generate the SSL keys"
    echo "######################################"
    php bin/console lexik:jwt:generate-keypair
else
    echo "Key are already generated."
fi

    echo "######################################"
    echo "Execute migrations ..."
    echo "######################################"
    php bin/console doctrine:migrations:migrate -n

    echo "######################################"
    echo "Execute fixtures load ..."
    echo "######################################"
    php bin/console doctrine:fixtures:load -n

# Start PHP-FPM (this replaces the CMD in Dockerfile)
php-fpm
