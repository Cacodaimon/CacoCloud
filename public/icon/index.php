<?php
chdir(__DIR__ . '/../../');
ini_set('session.use_cookies', 0);
require 'vendor/autoload.php';

\Caco\MiniAR::setDefaultPdo($pdo = new \PDO('sqlite:database/app.sqlite3'));

$app = new \Slim\Slim();
$app->view(new \Caco\Slim\JsonView);

$app->get('/feed/:id',     '\Caco\Icon\REST:oneFeed')    ->conditions(['id' => '\d+']);
$app->get('/bookmark/:id', '\Caco\Icon\REST:oneBookmark')->conditions(['id' => '\d+']);

$app->run();