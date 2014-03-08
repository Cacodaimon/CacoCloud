angular.module('caco.general.crtl', ['caco.Credentials', 'caco.InstallWebApp'])
    .controller('AccountCrtl', function ($rootScope, $scope, $location, Credentials) {
        $scope.basicAuthPersist = 0;

        $scope.login = function () {
            Credentials.key.server = $scope.keyServer;

            Credentials.basicAuth = {
                user: $scope.basicAuthUser,
                pass: $scope.basicAuthPass
            };

            if ($scope.basicAuthPersist > 0) {
                Credentials.persist($scope.basicAuthPersist);
            }

            $location.path('/welcome');
        };

        if ($location.path() === '/logout') {
            Credentials.logout();
            $location.path('/login');
        }
    });