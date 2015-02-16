<?php
chdir(__DIR__ . '/../../');
require 'vendor/autoload.php';

\Caco\MiniAR::setDefaultPdo($pdo = new \PDO('sqlite:database/app.sqlite3'));

$app = new \Slim\Slim();
$app->config('templates.path', __DIR__ . '/views/');
$app->add(new \Slim\Middleware\SessionCookie([
      'expires' => '20 minutes',
      'path' => '/',
      'domain' => null,
      'secure' => false,
      'httponly' => false,
      'name' => 'slim_session',
      'secret' => 'CHANGE_ME',
      'cipher' => MCRYPT_RIJNDAEL_256,
      'cipher_mode' => MCRYPT_MODE_CBC
 ]));
$app->flashKeep();

$app->get('/', function () use ($app) {
        $app->render('./step-requirements.phtml', [
            'progress'           => 25,
            'section'            => 'requirements',
            'https'              => $app->request()->getScheme() == 'https',
            'imap'               => extension_loaded('imap'),
            'mcrypt'             => extension_loaded('mcrypt'),
            'openssl'            => extension_loaded('openssl'),
            'pdo_sqlite'         => extension_loaded('pdo_sqlite'),
            'pcre'               => extension_loaded('pcre'),
            'curl'               => extension_loaded('curl'),
            'mbstring'           => extension_loaded('mbstring'),
            'iconv'              => extension_loaded('iconv'),
            'installDirWritable' => is_writable(__DIR__),
            'php_5_4'            => version_compare(PHP_VERSION, '5.4.0', '>='),
        ]);
    });

$app->get('/database', function () use ($app, $pdo) {
        if (installationFinished()) {
            $app->redirect('already-installed');

            return;
        }

        $dbFound         = tabelsExsits($pdo);
        $dbCreated       = false;
        $errorInfo       = '';
        $errorCode       = 0;
        $databaseVersion = 0;

        if (!$dbFound) {
            $sql       = file_get_contents('database/create.sql');
            $pdo->exec($sql);
            $errorCode = $pdo->errorCode();
            $errorInfo = $pdo->errorInfo();
            $dbCreated = tabelsExsits($pdo);
        }

        if ($errorCode == '00000') {
            $config = new \Caco\Config\Model\Config;
            $config->readKey('database-version');
            $databaseVersion = $config->value;
        }

        $app->render('./step-database.phtml', [
            'progress'        => 50,
            'section'         => 'database',
            'dbFound'         => $dbFound,
            'dbCreated'       => $dbCreated,
            'errorInfo'       => $errorInfo,
            'errorCode'       => $errorCode,
            'databaseVersion' => $databaseVersion,
        ]);
    });

$app->get('/user', function () use ($app, $pdo) {
        $app->render('./step-user.phtml', [
            'progress' => 75,
            'section'  => 'user',
        ]);
    });

$app->post('/user', function () use ($app, $pdo) {
        if (installationFinished()) {
            $app->redirect('already-installed');

            return;
        }

        $userName        = $app->request()->post('userName');
        $password        = $app->request()->post('password');
        $passwordConfirm = $app->request()->post('passwordConfirm');

        if ($password != $passwordConfirm) {
            $app->flash('warning', 'The password did not matched the confirmation password!');
            $app->redirect('user');

            return;
        }

        $user = new Caco\Slim\Auth\Model\User;
        $user->userName = $userName;
        $user->setPassword($password);
        $user->save();

        $app->flash('success', ' Your user account has been added successfully. ');
        $app->redirect('finish');
    });

$app->get('/finish', function () use ($app, $pdo) {
        touch(__DIR__ . '/finished');

        $config = new \Caco\Config\Model\Config;
        $config->readKey('api-url');
        $config->value = $app->request()->getUrl() . '/api';
        $config->save();

        $app->render('./step-finish.phtml', [
              'progress' => 100,
              'section'  => 'finish',
              ]);
    });

$app->get('/update', function () use ($app, $pdo) {
        $config = new \Caco\Config\Model\Config;
        $config->readKey('database-version');
        $databaseVersion = $config->value;

        if ($databaseVersion < 2) {
            $sql = file_get_contents('database/create.sql');
            $pdo->exec($sql);
            $app->flash('success', 'Update: Updated to database Version 2!');
        } else {
            $app->flash('success', 'Update: Nothing to do&hellip;');
        }

        $app->redirect('finish');
    });

$app->get('/already-installed', function () use ($app, $pdo) {
        $app->flash('danger', 'It seems that CacoCloud has been already installed! ');
        $app->flash('warning', 'If not please removed the <code>/public/finished</code> file! ');
        $app->redirect('/');
    });


$app->run();

/**
 * Returns true if the finished file exists!
 *
 * @return bool
 */
function installationFinished()
{
    return file_exists(__DIR__ . '/finished');
}

/**
 * Checks if all CacoCloud Tables exists.
 *
 * @param PDO $pdo
 * @return bool
 */
function tabelsExsits(PDO $pdo)
{
    return  tableExisits($pdo, 'user') &&
            tableExisits($pdo, 'container') &&
            tableExisits($pdo, 'bookmark') &&
            tableExisits($pdo, 'feed') &&
            tableExisits($pdo, 'item') &&
            tableExisits($pdo, 'config') &&
            tableExisits($pdo, 'mailaccount');
}

/**
 * Checks if the given table exists.
 *
 * @param PDO $pdo
 * @param string $tableName
 * @return int
 */
function tableExisits(PDO $pdo, $tableName)
{
    $sth = $pdo->prepare('SELECT COUNT(1) FROM `sqlite_master` WHERE `type`=\'table\' AND `name`= ?;');
    $sth->execute([$tableName]);

    return intval($sth->fetchColumn());
}