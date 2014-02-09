angular.module('caco.bookmark', ['ui.router', 'caco.bookmark.crtl'])
    .config(function($stateProvider) {
        $stateProvider
            .state('bookmark',      {                           templateUrl: 'views/bookmark/layout.html'                             })
            .state('bookmark.list', {url: '/bookmark',          templateUrl: 'views/bookmark/list.html',    controller: 'BookMarkCrtl'})
            .state('bookmark.add',  {url: '/bookmark/add',      templateUrl: 'views/bookmark/add.html',     controller: 'BookMarkCrtl'})
            .state('bookmark.edit', {url: '/bookmark/edit/:id', templateUrl: 'views/bookmark/edit.html',    controller: 'BookMarkCrtl'});
    })
    .run(function($rootScope) {
        $rootScope.moduleBookmark = true;
    });