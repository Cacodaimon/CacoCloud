<?php
chdir(__DIR__ . '/../../../');
ini_set('session.use_cookies', 0);
require 'vendor/autoload.php';

\Caco\MiniAR::setDefaultPdo(new \PDO('sqlite:database/app.sqlite3'));

$app = new \Slim\Slim();
$app->view(new \Caco\Slim\JsonView);
$app->add($auth = new \Caco\Slim\Auth\Basic);
$auth->setRealm('Caco Cloud');

$password = new \Caco\Password\REST;
$password->register($app);

$bookmark = new \Caco\Bookmark\REST;
$bookmark->register($app);

$feed = new \Caco\Feed\REST;
$feed->register($app);

$config = new \Caco\Config\REST;
$config->register($app);

$mail = new \Caco\Mail\REST;
$mail->register($app);

$exports = new \Caco\Exports\REST;
$exports->register($app);

$app->run();