#!/bin/bash

##### Initializes the project after deployment #########

cd /var/www/snowtricks-test || exit
source "$(dirname $0)/init-database.sh"

cd /var/www/snowtricks-test || exit
source "$(dirname $0)/init-medias.sh"