angular.module('caco.feed', ['ui.router', 'caco.feed.crtl', 'caco.feed.filter'])
    .config(function($stateProvider) {
        $stateProvider
            .state('feed',                         {                                     templateUrl: 'views/feed/main.html',                   controller: 'FeedCrtl'      })
            .state('feed.overview',                {url: '/feed/show',                   templateUrl: 'views/feed/main/list.html',              controller: 'ItemsCrtl'     })
            .state('feed.list',                    {url: '/feed/show/:id',               templateUrl: 'views/feed/main/list.html',              controller: 'ItemsCrtl'     })
            .state('feed.item',                    {url: '/feed/show/:id/item/:id_item', templateUrl: 'views/feed/main/item.html',              controller: 'ItemCrtl'      })
            .state('feed-manage',                  {                                     templateUrl: 'views/feed/manage.html'                                              })
            .state('feed-manage.list',             {url: '/feed/manage',                 templateUrl: 'views/feed/manage/list.html',            controller: 'FeedManageCrtl'})
            .state('feed-manage.add',              {url: '/feed/manage/add',             templateUrl: 'views/feed/manage/add.html',             controller: 'FeedManageCrtl'})
            .state('feed-manage.edit',             {url: '/feed/manage/edit/:id',        templateUrl: 'views/feed/manage/edit.html',            controller: 'FeedManageCrtl'})
            .state('feed-manage.update-interval',  {url: '/feed/manage/update-interval', templateUrl: 'views/feed/manage/update-interval.html', controller: 'FeedManageUpdateIntervalCrtl'})
            .state('feed-manage.auto-cleanup',     {url: '/feed/manage/auto-cleanup',    templateUrl: 'views/feed/manage/auto-cleanup.html',    controller: 'FeedManageAutoCleanupCrtl'});
    });