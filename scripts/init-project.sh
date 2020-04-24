#!/bin/bash

# Install dependencies
composer install --no-interaction
yarn install --no-interaction

# Prepare database with Docrtine
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

# Clear media directory
rm -R -d public/media/avatars
rm -R -d public/media/tricks_media

# Move media samples to media directory
cp -p -r public/media/sampleFiles/avatars public/media/avatars
cp -p -r public/media/sampleFiles/tricks_media public/media/tricks_media

#Fixtures
php bin/console doctrine:fixtures:load --no-interaction

#Encore build
yarn encore dev