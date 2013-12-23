angular.module('caco.password.crtl', ['caco.password.REST'])
    .controller('PasswordCrtl', function ($rootScope, $scope, $stateParams, $location, $http, PasswordREST, Credentials, Alerts) {
        $rootScope.module = 'password';

        if (Credentials.emptyServerKey()) {
            $location.path('/password/auth');
        }

        if ($stateParams.id) {
            PasswordREST.one({id: $stateParams.id}, function (data) {
                if ($scope.clientKeyDecrypt) {
                    $scope.container = decryptContainer(data.response, $scope.clientKeyDecrypt);
                } else {
                    $scope.container = data.response;
                }
            });
        } else {
            PasswordREST.all({}, function (data) {
                $scope.passwords = data.response;
            });
        }

        $scope.$watch('clientKeyDecrypt', function (clientKeyDecrypt) {
            if (!$scope.container) {
                return;
            }

            $scope.decryptedContainer = decryptContainer($scope.container, clientKeyDecrypt);
        });

        $scope.$watchCollection('container', function (container) {
            if (container && container.password) {
                var score = zxcvbn(container.password).score;
                $scope.passwordScore = score * 25;
                var types = ['danger', 'danger', 'warning', 'warning', 'success'];
                $scope.passwordScoreText = types[score];
            };
        });

        $scope.auth = function () {
            Credentials.key.server = $scope.serverKey;
            $location.path('/password');
        };

        $scope.add = function () {
            var container = $scope.container;

            encryptContainer(container, $scope.clientKey);
            container.date = Math.round(new Date().getTime() / 1000);

            PasswordREST.add({}, container, function () {
                $location.path('/password');
            }, function () {
                Alerts.addDanger('Password has not been added!');
            });
        };

        $scope.edit = function () {
            var container = $scope.decryptedContainer;

            encryptContainer(container, $scope.clientKey);

            PasswordREST.edit({id: container.id}, container, function () {
                $location.path('/password');
            }, function () {
                Alerts.addDanger('Password has not been edited!');
            });
        };

        $scope.delete = function (id) {
            if (!confirm('Confirm delete')) {
                return;
            }

            PasswordREST.remove({id: id}, {}, function(data) {
                if (data.status != 200) {
                    return;
                }

                for (var i = $scope.passwords.length - 1; i >= 0; i--) {
                    if ($scope.passwords[i].id != id) {
                        continue;
                    }

                    $scope.passwords.splice(i, 1);
                }
            }, function () {
                Alerts.addDanger('Password has not been deleted!');
            });
        };

        var decryptContainer = function (container, key) {
            var dContainer = JSON.parse(JSON.stringify(container)); //-- clone

            if (dContainer.password) {
                var password = CryptoJS.AES.decrypt(dContainer.password, key);
                dContainer.password = password.toString(CryptoJS.enc.Utf8);
            }

            if (dContainer.key && dContainer.key.public && dContainer.key.private) {
                var pubKey = CryptoJS.AES.decrypt(dContainer.key.public, key);
                dContainer.key.public = pubKey.toString(CryptoJS.enc.Utf8);

                var privKey = CryptoJS.AES.decrypt(dContainer.key.private, key);
                dContainer.key.private = privKey.toString(CryptoJS.enc.Utf8);
            }

            if (dContainer.license) {
                var license = CryptoJS.AES.decrypt(dContainer.license, key);
                dContainer.license = license.toString(CryptoJS.enc.Utf8);
            }

            return dContainer;
        };

        var encryptContainer = function (container, key) {
            if (container.password) {
                container.password = CryptoJS.AES.encrypt(container.password, key).toString();
            }

            if (container.key && container.key.public && container.key.private) {
                container.key.public = CryptoJS.AES.encrypt(container.key.public, key).toString();
                container.key.private = CryptoJS.AES.encrypt(container.key.private, key).toString();
            }

            if (container.license) {
                container.license = CryptoJS.AES.encrypt(container.license, key).toString();
            }
        };
    });