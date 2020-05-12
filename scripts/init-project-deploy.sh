#!/bin/bash

##### Initializes the project after deployment #########

source "$(dirname $0)/init-database.sh"
source "$(dirname $0)/init-assets.sh"

cd /var/www/snowtricks-test || exit
source "$(dirname $0)/init-medias.sh"