angular.module('caco.feed.crtl')
    .controller('ItemQueueCrtl', function ($rootScope, $scope, $stateParams, $location, Items) {
        $rootScope.module = 'feed';

        Items.dequeue(function (item, found) {
            $scope.item = item;
            $scope.notFound = !found;
        });

        $scope.dequeue = function () {
            Items.dequeue(function (item, found) {
                found && $rootScope.$broadcast('ItemDequeued', item);
                $scope.notFound = !found;
            });
        };

        $rootScope.$on('ItemDequeued', function (event, item) {
            $scope.item = item;
        });


        $scope.addToBookmark = function (item) {
            BookMarkREST.add({}, {name: item.title, url: item.url}, function () {
                $location.path('/bookmark');
            });
        };
    });