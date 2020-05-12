#!/bin/bash

# Clears the database
php bin/console doctrine:schema:drop --force --no-interaction

# Prepare database with Docrtine
php bin/console doctrine:migrations:migrate --no-interaction

#Fixtures
php bin/console doctrine:fixtures:load --no-interaction