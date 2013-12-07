angular.module('caco.feed.crtl')
    .controller('FeedManageAutoCleanupCrtl', function ($rootScope, $scope, $stateParams, $location, ConfigREST) {
        $rootScope.module = 'feed';
        $rootScope.modulePath = $location.path();

        ConfigREST.one({key: 'auto-cleanup'}, function (data) {
            $scope.config = [];
            for (var i = data.response.length - 1; i >= 0; i--) {
                var row = data.response[i];
                $scope.config[row.key] = row.value;
            }
        });

        $scope.save = function () {
            for (var key in $scope.config) {
                if (!$scope.config.hasOwnProperty(key)) {
                    continue;
                }

                ConfigREST.edit({key: key}, {key: key, value: $scope.config[key]});
            }
        };
    });