angular.module('caco.feed.crtl')
    .controller('FeedManageUpdateIntervalCrtl', function ($rootScope, $scope, $stateParams, $location, ConfigREST, FeedCalculateUpdateIntervalsREST) {
        $rootScope.module = 'feed';
        $rootScope.modulePath = $location.path();

        ConfigREST.one({key: 'update-interval'}, function (data) {
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

        $scope.calculating = false;
        $scope.calculated = false;
        $scope.calculateUpdateInterval = function () {
            $scope.calculated = false;
            $scope.calculating = true;
            FeedCalculateUpdateIntervalsREST.perform({}, function () {
                $scope.calculating = false;
                $scope.calculated = true;
            });
        };
    });