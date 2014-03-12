<?php
chdir(__DIR__ . '/../../../');
ini_set('session.use_cookies', 0);
require 'vendor/autoload.php';

\Caco\MiniAR::setDefaultPdo($pdo = new \PDO('sqlite:database/app.sqlite3'));
$pdo->exec('PRAGMA foreign_keys = ON');

$app = new \Slim\Slim();
$app->view(new \Caco\Slim\JsonView);
$app->add($auth = new \Caco\Slim\Auth\Basic);
$auth->setRealm('Caco Cloud');

$app->group('/password', function () use ($app) {
        $app->get('/:key/:id',    '\Caco\Password\REST:one')   ->conditions(['id' => '\d+']);
        $app->get('/:key',        '\Caco\Password\REST:all');
        $app->post('/:key',       '\Caco\Password\REST:add');
        $app->delete('/:key/:id', '\Caco\Password\REST:delete')->conditions(['id' => '\d+']);
        $app->put('/:key/:id',    '\Caco\Password\REST:edit')  ->conditions(['id' => '\d+']);
    });

$app->group('/bookmark', function () use ($app) {
        $app->get('/:id',    '\Caco\Bookmark\REST:one')   ->conditions(['id' => '\d+']);
        $app->get('',        '\Caco\Bookmark\REST:all');
        $app->post('',       '\Caco\Bookmark\REST:add');
        $app->delete('/:id', '\Caco\Bookmark\REST:delete')->conditions(['id' => '\d+']);
        $app->put('/:id',    '\Caco\Bookmark\REST:edit')  ->conditions(['id' => '\d+']);
    });

$app->group('/config', function () use ($app) {
        $app->get('/:key',    '\Caco\Config\REST:one');
        $app->get('',         '\Caco\Config\REST:all');
        $app->post('',        '\Caco\Config\REST:add');
        $app->delete('/:key', '\Caco\Config\REST:delete');
        $app->put('/:key',    '\Caco\Config\REST:edit');
    });

$app->group('/feed', function () use ($app) {
        $app->get('/update',                    '\Caco\Feed\REST:updateAllFeeds')->conditions(['id' => '\d+']);
        $app->get('/update/:id',                '\Caco\Feed\REST:updateFeed')    ->conditions(['id' => '\d+']);
        $app->get('/:id',                       '\Caco\Feed\REST:getFeed')       ->conditions(['id' => '\d+']);
        $app->get('',                           '\Caco\Feed\REST:getAllFeeds');
        $app->get('/item',                      '\Caco\Feed\REST:getAllItems');
        $app->get('/:id/item',                  '\Caco\Feed\REST:getItems')      ->conditions(['id' => '\d+']);
        $app->get('/item/:id',                  '\Caco\Feed\REST:getItem')       ->conditions(['id' => '\d+']);
        $app->get('/item/queue',                '\Caco\Feed\REST:dequeueItem');
        $app->put('/:id',                       '\Caco\Feed\REST:editFeed')      ->conditions(['id' => '\d+']);
        $app->post('',                          '\Caco\Feed\REST:addFeed');
        $app->post('/item/queue/:id',           '\Caco\Feed\REST:enqueueItem')   ->conditions(['id' => '\d+']);
        $app->delete('/:id',                    '\Caco\Feed\REST:deleteFeed')    ->conditions(['id' => '\d+']);
        $app->delete('/item/:id',               '\Caco\Feed\REST:deleteItem')    ->conditions(['id' => '\d+']);
        $app->get('/calculate-update-interval', '\Caco\Feed\REST:calculateUpdateInterval');
    });

$app->group('/mail', function () use ($app) {
        $app->get('/:key/account/:id/mailbox/:mailBox/mail/:uniqueId',    '\Caco\Mail\REST:showMail');
        $app->delete('/:key/account/:id/mailbox/:mailBox/mail/:uniqueId', '\Caco\Mail\REST:deleteMail');
        $app->get('/:key/account/:id/mailbox/:mailBox',                   '\Caco\Mail\REST:mailHeaders');
        $app->get('/:key/account/:id/mailbox',                            '\Caco\Mail\REST:mailBoxes');
        $app->post('/:key/account/:id/send',                              '\Caco\Mail\REST:sendMail');
        $app->get('/:key/account',                                        '\Caco\Mail\REST:allAccounts');
        $app->get('/:key/account/:id',                                    '\Caco\Mail\REST:oneAccount');
        $app->post('/:key/account',                                       '\Caco\Mail\REST:addAccount');
        $app->put('/:key/account/:id',                                    '\Caco\Mail\REST:editAccount');
        $app->delete('/:key/account/:id',                                 '\Caco\Mail\REST:deleteAccount');
    });

$app->group('/export', function () use ($app) {
        $app->get('/feed/opml',     '\Caco\Exports\REST:getAllFeedsOpml');
        $app->get('/feed/:id/atom', '\Caco\Exports\REST:getFeedItemsAtom')->conditions(['id' => '\d+']);
        $app->get('/bookmark/xbel', '\Caco\Exports\REST:getBookmarksXbel')->conditions(['id' => '\d+']);
        $app->get('/bookmark/html', '\Caco\Exports\REST:getBookmarksHtml')->conditions(['id' => '\d+']);
    });

$app->run();