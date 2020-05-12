#!/bin/bash

##### Initializes the project after deployment #########

cd /var/www/snowtricks-test || exit
source "/var/www/snowtricks-test/scripts/init-database.sh"

cd /var/www/snowtricks-test || exit
source "/var/www/snowtricks-test/scripts/init-medias.sh"