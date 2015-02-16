#!/bin/bash

EXCLUDE_FILES="--exclude=CacoCloud.tar --exclude=.git --exclude=CacoCloud.tar.* --exclude=Gruntfile.js --exclude=package.json --exclude=composer.lock --exclude=composer.phar --exclude=composer.json --exclude=nohup.out --exclude=spec --exclude=reports --exclude=assets  --exclude=node_modules --exclude=*.sh --exclude=public/install/finished --exclude=database/app.sqlite* --exclude=public/icons/bookmark/* --exclude=public/icons/feed/*  --exclude=vendor/simplepie/simplepie/demo --exclude=vendor/simplepie/simplepie/tests  --exclude=vendor/slim/slim/tests --exclude=vendor/phpmailer/phpmailer/examples --exclude=vendor/guzzlehttp/guzzle/tests --exclude=vendor/react/promise/tests --exclude=vendor/guzzlehttp/ringphp/tests --exclude=vendor/guzzlehttp/streams/tests --exclude=vendor/arthurhoaro/favicon/resources/tests"
FILE_NAME="CacoCloud"
GZIP="czpvf $FILE_NAME.tar.gz"

tar -$GZIP * $EXCLUDE_FILES