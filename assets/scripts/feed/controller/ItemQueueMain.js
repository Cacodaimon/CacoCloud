angular.module('caco.feed.crtl')
    .controller('ItemQueueMainCrtl', function ($rootScope, $scope, $stateParams, $location, Items) {
        $rootScope.module = 'feed';

        $scope.dequeue = function () {
            Items.dequeue(function (item, found) {
                found && $rootScope.$broadcast('ItemDequeued', item);
                $scope.notFound = !found;
            });
        };
    });