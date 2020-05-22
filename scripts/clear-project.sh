#!/bin/bash

cd /var/www/snowtricks-test || exit
find . ! -name '.env.local' -type d -exec rm -r {} +
find . ! -name '.env.local' -type f -exec rm -f {} +