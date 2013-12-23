angular.module('caco.mail.crtl')
    .controller('MailReplyCrtl', function ($rootScope, $scope, $stateParams, $location, MailAccountREST, MailREST, SendMailREST, Credentials, Alerts) {
        if (Credentials.emptyEmailKey()) {
            $location.path('/mail/auth');
        }

        $rootScope.module = 'mail';

        MailAccountREST.one({id: $stateParams.id}, function (data) {
            $scope.accounts = [data.response];
        }, function () {
            Alerts.addDanger('Could not find a account for sending the E-Mail!');
        });

        MailREST.one($stateParams, function (data) {
            var mail = data.response;
            mail.subject = 'Re: ' + mail.subject;

            mail.to = mail.from;
            if (mail.to.indexOf('<') != -1) {
                mail.to = mail.to.substring(mail.to.indexOf('<') + 1, mail.to.length - 1);
            }

            mail.from = null;

            var body = '';
            var lines = (mail.bodyPlainText ? mail.bodyPlainText : mail.bodyHtml).split('\n');
            for (var i = 0; i < lines.length; i++) {
                body += '> ' + lines[i];
            }

            mail.body = body;

            $scope.mail = mail;
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