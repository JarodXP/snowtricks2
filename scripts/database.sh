#!/bin/bash

# Prepare database with Doctrine
cd /var/www/snowtricks-test || exit
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

#Fixtures
php bin/console doctrine:fixtures:load --no-interaction