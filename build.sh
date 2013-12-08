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
if [ -f database/app.sqlite3 ]; then
  echo "Skipped creating the database..."
else
  cat database/create.sql | sqlite3 database/app.sqlite3
fi

echo "Create a new user"
echo -n "User name: "
read -r USER_NAME
echo -n "Password: "
read -r -s PASSWORD
php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a create -u $USER_NAME -p $PASSWORD
php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a list