#!/bin/bash

# Clear media directory
[ -d public/media/avatars ] rm -R -d public/media/avatars
[ -d public/media/tricks_media ] rm -R -d public/media/tricks_media

# Move media samples to media directory
cp -p -r public/media/sampleFiles/avatars public/media/avatars
cp -p -r public/media/sampleFiles/tricks_media public/media/tricks_media