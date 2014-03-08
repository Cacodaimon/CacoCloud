#!/bin/bash
. ./tools.sh

#
# Easy initial build script.
#

echo_blue "Install grunt modules"
npm_module_install grunt-contrib-uglify
npm_module_install grunt-contrib-cssmin
npm_module_install grunt-contrib-watch
npm_module_install grunt-contrib-htmlmin
npm_module_install grunt-contrib-copy

echo_blue "Running grunt"
grunt vendor
grunt

if [ ! -f composer.phar ]; then
  echo_blue "Installing composer"
  curl -sS https://getcomposer.org/installer | php
else
  echo_blue "Updating composer"
  php composer.phar self-update
fi

echo_blue "Running composer"
php composer.phar update

if [ -f database/app.sqlite3 ]; then
  echo_yellow "Skipped creating the database..."
else
  echo_blue "Creating SQLite database"
  cat database/create.sql | sqlite3 database/app.sqlite3

  echo_blue "Create a new user"
  echo -n "User name: "
  read -r USER_NAME
  echo -n "Password: "
  read -r -s PASSWORD
  php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a create -u $USER_NAME -p $PASSWORD
fi

php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a list

echo_blue "Set the api url (https://example.com/api/1)"
ID=`php cli/run_cli.php --cli="Caco\\Config\\CLI\\Manage" -a list | grep api-url | cut -d' ' -f 3`
echo -n "URL: "
read -r URL
php cli/run_cli.php --cli=Caco\\Config\\CLI\\Manage -a update -i $ID -v $URL

#./run_tests.sh
