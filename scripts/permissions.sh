#!/bin/bash

#Sets permissions
sudo find /var/www/snowtricks-test/ -type d -exec chmod 755 {} +
sudo find /var/www/snowtricks-test/ -type f -exec chmod 644 {} +

#Set var/log/ & var/cache permission
chmod -R 777 /var/www/snowtricks-test/var/log/
chmod -R 777 /var/www/snowtricks-test/var/cache/