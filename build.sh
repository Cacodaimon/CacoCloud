#!/bin/bash

#
# Easy initial build script.
#

function grunt_module_install () {
  MODULE_NAME="$1"

  if [ -d "node_modules/$MODULE_NAME" ]; then
    echo "Module $MODULE_NAME is already installed, skipping..."
  else
    npm install "$MODULE_NAME"  --save-dev
  fi
}

echo "Install grunt modules"
grunt_module_install grunt-contrib-uglify
grunt_module_install grunt-contrib-cssmin
grunt_module_install grunt-contrib-watch
grunt_module_install grunt-contrib-htmlmin
grunt_module_install grunt-contrib-copy

echo "Running grunt"
grunt vendor
grunt

if [ ! -f composer.phar ]; then
  echo "Installing composer"
  curl -sS https://getcomposer.org/installer | php
fi

echo "Running composer"
php composer.phar update

if [ -f database/app.sqlite3 ]; then
  echo "Skipped creating the database..."
else
  echo "Creating SQLite database"
  cat database/create.sql | sqlite3 database/app.sqlite3

  echo "Create a new user"
  echo -n "User name: "
  read -r USER_NAME
  echo -n "Password: "
  read -r -s PASSWORD
  php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a create -u $USER_NAME -p $PASSWORD
fi

php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a list