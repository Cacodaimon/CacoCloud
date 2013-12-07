angular.module('caco.mail.crtl')
    .controller('MailAuthCrtl', function ($scope, $location, Credentials) {
        $scope.auth = function () {
            Credentials.key.email = $scope.emailKey;
            $location.path('/mail');
        };
    })