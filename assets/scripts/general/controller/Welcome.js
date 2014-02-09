angular.module('caco.general.crtl')
    .controller('WelcomeCrtl', function ($scope, $rootScope, WebApp) {
        if (WebApp.isWebAppDevice()) {
            WebApp.checkInstalled(function (installed) {
                !installed && $scope.$apply(function () {
                    $scope.webAppInstallPossible = true;
                });
            });
        } else {
            $scope.webAppInstallPossible = false;
        }

        try {
            angular.module("caco.password");
            $rootScope.passwd = true;
        } catch(err) {
            $rootScope.passwd = false;
        }

        $scope.install = function () {
            WebApp.install();
        };
    });