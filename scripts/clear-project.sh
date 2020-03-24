#!/bin/bash

cd /var/www/ || exit
rm -rf snowtricks
mkdir snowtricks
cp .env.local snowtricks/.env.local