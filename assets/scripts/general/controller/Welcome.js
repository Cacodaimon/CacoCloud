angular.module('caco.general.welcome.crtl', ['caco.InstallWebApp'])
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

        $scope.install = function () {
            WebApp.install();
        };
    });