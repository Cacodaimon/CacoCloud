angular.module('caco.feed.crtl')
    .controller('FeedCrtl', function ($rootScope, $scope, $stateParams, $location, Items, Feeds) {
        $rootScope.module = 'feed';

        $scope.currentFeedId = 0;
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
            $scope.currentFeedId = toParams.id ? toParams.id : 0;
        });

        $scope.$on('FeedsUpdated', function () {
            Feeds.get(function (feeds) {
                $scope.feeds = feeds;
            });
        });

        Feeds.get(function (feeds) {
            $scope.feeds = feeds;
        });
    });