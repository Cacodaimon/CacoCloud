<?php
if (PHP_SAPI !== 'cli') {
    die('This is a cli!');
}

require  __DIR__ . '/../vendor/autoload.php';
\Caco\MiniAR::setDefaultPdo(new \PDO('sqlite:' . __DIR__ . '/../database/app.sqlite3'));

$opts = getopt('c:', ['cli:']);

$cliClassName = empty($opts['c']) ? $opts['cli'] : $opts['c'];
/** @var Caco\CLI\ICLI $cliClass */
$cliClass = new $cliClassName;

if (!($cliClass instanceof Caco\CLI\ICLI)) {
    die('Given class is not a cli!');
}

$cliClass->init();

try {
    exit($cliClass->run());
} catch (InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
}
