angular.module('caco.mail.crtl')
    .controller('MailSendCrtl', function ($rootScope, $scope, $stateParams, $location, MailAccountREST, SendMailREST, Credentials, Alerts) {
        if (Credentials.emptyEmailKey()) {
            $location.path('/mail/auth');
        }

        $rootScope.module = 'mail';

        MailAccountREST.all({}, function (data) {
            $scope.accounts = data.response;
        }, function () {
            Alerts.addDanger('Could not find a account for sending the E-Mail!');
        });

        $scope.send = function () {
            SendMailREST.send({id: $scope.mail.fromId}, $scope.mail, function () {
                Alerts.addSuccess('The E-Mail has been send!');
                $location.path('/mail');
            }, function () {
                Alerts.addDanger('Could not send the mail!');
            });
        };
    });