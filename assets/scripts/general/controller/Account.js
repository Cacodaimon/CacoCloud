angular.module('caco.general.account.crtl', ['caco.Credentials'])
    .controller('AccountCrtl', function ($rootScope, $scope, $location, Credentials) {
        $scope.login = function () {
            Credentials.key.server = $scope.keyServer;

            Credentials.basicAuth = {
                user: $scope.basicAuthUser,
                pass: $scope.basicAuthPass
            };

            if ($scope.basicAuthPersist) {
                Credentials.persist();
            }

            $location.path('/welcome');
        };

        if ($location.path() === '/logout') {
            Credentials.logout();
            $location.path('/login');
        }
    });