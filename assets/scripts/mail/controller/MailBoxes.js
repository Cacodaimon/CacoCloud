angular.module('caco.mail.crtl')
    .controller('MailBoxesCrtl', function ($rootScope, $scope, $stateParams, $location, MailAccountREST, MailBoxesREST, Credentials) {
        if (Credentials.emptyEmailKey()) {
            $location.path('/mail/auth');
        }

        $rootScope.module = 'mail';

        MailAccountREST.all({}, function (data) {
            $scope.accounts = [];
            for (var i = data.response.length - 1; i >= 0; i--) {
                MailBoxesREST.all({id: data.response[i].id}, function (data1) {
                    $scope.accounts.push(data1.response);
                });
            }
        });

        $scope.$on('MailChanged', function (event, message) {
            MailBoxesREST.all(message, function (data) {
                for (var i = $scope.accounts.length - 1; i >= 0; i--) {
                    if ($scope.accounts[i].id == message.id) {
                        $scope.accounts[i] = data.response;
                    }
                }
            });
        });



        $scope.refresh = function (accountId) {
            for (var i = $scope.accounts.length - 1; i >= 0; i--) {
                if ($scope.accounts[i].id == accountId) {
                    $scope.accounts[i].refresh = true;
                }
            }
            MailBoxesREST.all({id: accountId}, function (data1) {
                for (var i = $scope.accounts.length - 1; i >= 0; i--) {
                    if ($scope.accounts[i].id == accountId) {
                        $scope.accounts[i] = data1.response;
                    }
                }
            });
        };
    });