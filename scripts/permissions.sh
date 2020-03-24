#!/bin/bash

#Sets permissions
sudo find /var/www/snowtricks/ -type d -exec chmod 755 {} +
sudo find /var/www/snowtricks/ -type f -exec chmod 644 {} +

#Set var log/open permission
chmod -R 777 /var/www/snowtricks/var/log/
chmod -R 777 /var/www/snowtricks/var/cache/