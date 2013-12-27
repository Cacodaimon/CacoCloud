#!/bin/bash
. ./tools.sh

#
# Easy run frisby.js tests script.
#

function install_node_modules () {
  echo_blue "Installing modules for running frisby.js"
  npm_module_install jasmine-node
  npm_module_install frisby
  npm_module_install sleep
}

function create_test_user () {
  echo_blue "Creating test user"
  php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a create -u TEST_USER -p TEST_PASSWORD
}

function delete_test_user () {
  echo_blue "Delete test user"
  php cli/run_cli.php --cli=Caco\\Slim\\Auth\\UserManagement -a delete -u TEST_USER
}

function start_php_build_in_http_server () {
  echo_blue "Starting PHP build in webserver"
  php -S 127.0.0.1:8000 -t public &
  PHP_SERVER_PID="$!"
}

function stop_php_build_in_http_server () {
  echo_blue "Stopping PHP build in webserver"
  kill "$PHP_SERVER_PID"
}

function run_tests () {
  echo_blue "Running the tests now..."
  node_modules/jasmine-node/bin/jasmine-node --junitreport spec/api/
}

install_node_modules
create_test_user
start_php_build_in_http_server
run_tests
stop_php_build_in_http_server
delete_test_user