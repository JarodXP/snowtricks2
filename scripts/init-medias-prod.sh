#!/bin/bash

# Clear media directory
rm -R -d /var/www/snowtricks/public/media/avatars
rm -R -d /var/www/snowtricks/public/media/tricks_media

# Move media samples to media directory
cp -p -r /var/www/snowtricks/public/media/sampleFiles/avatars public/media/avatars
cp -p -r /var/www/snowtricks/public/media/sampleFiles/tricks_media public/media/tricks_media