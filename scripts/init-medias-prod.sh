#!/bin/bash

#Clear medias directories
if [ -d /var/www/snowtricks/public/media/avatars ];
then
  rm -R -d /var/www/snowtricks/public/media/avatars
fi

if [ -d /var/www/snowtricks/public/media/tricks_media ];
then
  rm -R -d /var/www/snowtricks/public/media/tricks_media
fi

# Move media samples to media directory
cp -p -r /var/www/snowtricks/public/media/sampleFiles/avatars /var/www/snowtricks/public/media/avatars
cp -p -r /var/www/snowtricks/public/media/sampleFiles/tricks_media /var/www/snowtricks/public/media/tricks_media