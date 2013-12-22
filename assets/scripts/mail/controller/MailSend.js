angular.module('caco.mail.crtl')
    .controller('MailSendCrtl', function ($rootScope, $scope, $stateParams, $location, MailAccountREST, SendMailREST, Credentials) {
        if (Credentials.emptyEmailKey()) {
            $location.path('/mail/auth');
        }

        $rootScope.module = 'mail';

        MailAccountREST.all({}, function (data) {
            $scope.accounts = data.response;
            console.log($scope.accounts);
        });

        $scope.send = function () {
            SendMailREST.send({id: $scope.mail.fromId}, $scope.mail, function () {
                alert('Mail has been send!');
                $location.path('/mail');
            });
        };
    });