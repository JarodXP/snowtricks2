#!/bin/bash

# Install dependencies
composer install --no-interaction
yarn install --no-interaction

#Encore build
yarn encore dev