#!/bin/bash

#
# Shared functions for build.sh and run_tests.sh
#

function echo_blue () {
  echo -e "\e[94m$1\e[39m"
}

function echo_green () {
  echo -e "\e[92m$1\e[39m"
}

function echo_yellow () {
  echo -e "\e[93m$1\e[39m"
}

function echo_red () {
  echo -e "\e[91m$1\e[39m"
}

function npm_module_install () {
  MODULE_NAME="$1"

  if [ -d "node_modules/$MODULE_NAME" ]; then
    echo_yellow "Module $MODULE_NAME is already installed, skipping..."
  else
    npm install "$MODULE_NAME"  --save-dev
  fi
}