#!/bin/bash

#Resets the database
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS snowtricks"
php bin/console doctrine:database:create

#Resets migrations files
rm -r src/Migrations/*
php bin/console make:migration --no-interaction

# Prepare database with Docrtine
php bin/console doctrine:migrations:migrate --no-interaction

#Fixtures
php bin/console doctrine:fixtures:load --no-interaction