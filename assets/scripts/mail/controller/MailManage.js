angular.module('caco.mail.crtl')
    .controller('MailManageCrtl', function ($rootScope, $scope, $stateParams, $location, MailAccountREST, Credentials, Alerts) {
        if (Credentials.emptyEmailKey()) {
            $location.path('/mail/auth');
        }

        $rootScope.module = 'mail';
        $rootScope.modulePath = $location.path();

        $scope.newAccount = {imap: {type: 1}, smtp: {authType: 'PLAIN', secure: 'SSL'}};

        if ($stateParams.id) {
            MailAccountREST.one({id: $stateParams.id}, function (data) {
                $scope.account = data.response;
            });
        } else {
            MailAccountREST.all({}, function (data) {
                $scope.accounts = data.response;
            });
        }

        $scope.add = function () {
            MailAccountREST.add({}, $scope.newAccount, function () {
                $location.path('/mail/manage');
            }, function () {
                Alerts.addDanger('Could not add the E-Mail account!');
            });
        };

        $scope.edit = function () {
            MailAccountREST.edit({id: $scope.account.id}, $scope.account, function () {
                MailAccountREST.all({}, function (data) {
                    $scope.accounts = data.response;
                });
                $location.path('/mail/manage');
            }, function () {
                Alerts.addDanger('Could not edit the E-Mail account!');
            });
        };

        $scope.delete = function (id) {
            if (!confirm('Confirm delete')) {
                return;
            }

            MailAccountREST.remove({id: id}, {}, function(data) {
                MailAccountREST.all({}, function (data) {
                    $scope.accounts = data.response;
                });
            }, function () {
                Alerts.addDanger('Could not delete the E-Mail account!');
            });
        };
    });