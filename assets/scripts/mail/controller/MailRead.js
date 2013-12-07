angular.module('caco.mail.crtl')
    .controller('MailReadCrtl', function ($rootScope, $scope, $stateParams, $location, MailREST) {
        MailREST.one($stateParams, function (data) {
            var mail = data.response;

            console.log(mail);
            if (!mail.seen) {
                $rootScope.$broadcast('MailChanged', {id: $stateParams.id, mailBox: $stateParams.mailBox});
            }

            if(mail.bodyHtml) {
                mail.bodyHtml = window.atob(mail.bodyHtml);
            }

            if (mail.bodyPlainText) {
                mail.bodyPlainText = window.atob(mail.bodyPlainText);
            }

            $scope.mail = mail;
        });
    });