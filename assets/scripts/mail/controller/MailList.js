angular.module('caco.mail.crtl')
    .controller('MailListCrtl', function ($rootScope, $scope, $stateParams, $location, MailHeadersREST, MailREST) {
        $rootScope.id = $stateParams.id;
        $rootScope.mailBoxBase64 = $stateParams.mailBoxBase64;

        $scope.goToPage = function (page) {
            $stateParams.page = page;
            MailHeadersREST.all($stateParams, function (data) {
                $scope.headers = data.response;
                $scope.page = data.page;
                $scope.pages = Math.floor(data.messagesTotal / data.messagesPerPage);
                window.scrollTo(0, 0);
            });
        };
        $scope.goToPage(1);

        $scope.delete = function (id, mailBox, uniqueId) {
            if (!confirm('Confirm delete')) {
                return;
            }

            MailREST.remove({id: id, mailBoxBase64: mailBox, uniqueId: uniqueId}, function (data) {
                if (!data.response) {
                    return;
                }

                for (var i = $scope.headers.length - 1; i >= 0; i--) {
                    if ($scope.headers[i].uniqueId != uniqueId) {
                        continue;
                    }

                    $scope.headers.splice(i, 1);
                }
                $rootScope.$broadcast('MailChanged', {id: id, mailBox: mailBox});
            });
        };
    });