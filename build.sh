#!/bin/bash

#
# Easy initial build script.
#

echo "Install grunt modules"
npm install grunt-contrib-uglify  --save-dev
npm install grunt-contrib-cssmin  --save-dev
npm install grunt-contrib-watch   --save-dev
npm install grunt-contrib-htmlmin --save-dev

echo "Running grunt"
grunt vendor
grunt

echo "Installing composer"
curl -sS https://getcomposer.org/installer | php

echo "Running composer"
php composer.phar update

echo "Creating SQLite database"
cat database/create.sql | sqlite3 database/app.sqlite3