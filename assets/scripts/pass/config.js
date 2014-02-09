angular.module('caco.password', ['ui.router', 'caco.password.crtl'])
    .config(function($stateProvider) {
        $stateProvider
            .state('password',      {                           templateUrl: 'views/password/layout.html'                           })
            .state('password.list', {url: '/password',          templateUrl: 'views/password/list.html',  controller: 'PasswordCrtl'})
            .state('password.add',  {url: '/password/add',      templateUrl: 'views/password/add.html',   controller: 'PasswordCrtl'})
            .state('password.edit', {url: '/password/edit/:id', templateUrl: 'views/password/edit.html',  controller: 'PasswordCrtl'})
            .state('password.auth', {url: '/password/auth',     templateUrl: 'views/password/auth.html',  controller: 'PasswordCrtl'});
    })
    .run(function($rootScope) {
        $rootScope.modulePassword = true;
    });