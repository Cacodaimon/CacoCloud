angular.module('caco', ['ngAnimate', 'ngSanitize', 'caco.Credentials', 'caco.filter', 'caco.general.crtl', 'caco.password', 'caco.bookmark', 'caco.feed', 'caco.mail', 'caco.ClientPaginate'])
    .config(function($stateProvider) {
        $stateProvider
            .state('general', {url: '',         templateUrl: 'views/general/welcome.html', controller: 'WelcomeCrtl'})
            .state('welcome', {url: '/welcome', templateUrl: 'views/general/welcome.html', controller: 'WelcomeCrtl'})
            .state('login',   {url: '/login',   templateUrl: 'views/general/login.html',   controller: 'AccountCrtl'})
            .state('logout',  {url: '/logout',  templateUrl: 'views/general/logout.html',  controller: 'AccountCrtl'})
            .state('about',   {url: '/about',   templateUrl: 'views/general/about.html'})
    })
    .config(function ($httpProvider) {
        $httpProvider.interceptors.push(function ($q, $location, $rootScope, Credentials) {
            return {
                request: function(config) {
                    config.headers.Authorization = Credentials.basicAuthHeader();

                    if ($rootScope.loading) {
                        $rootScope.loading++
                    } else {
                        $rootScope.loading = 1;
                    }

                    return config || $q.when(config);
                },
                response: function (response) {
                    $rootScope.loading && $rootScope.loading--;

                    return response;
                },
                responseError: function (rejection) {
                    $rootScope.loading && $rootScope.loading--;

                    if(rejection.status === 401) {
                        Credentials.init();
                        $location.path('/login');
                    }

                    return $q.reject(rejection);
                }
            };
        })
    })
    .run(function($rootScope, $location, Credentials) {
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
            if (Credentials.empty() && toState.url != '/login') {
                $location.path('/login');
                event.preventDefault();
            }
        });
    });