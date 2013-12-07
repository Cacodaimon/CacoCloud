angular.module('caco.feed.crtl')
    .controller('FeedManageCrtl', function ($rootScope, $scope, $stateParams, $location, Feeds, FeedREST, FeedUrlLookupREST) {
        $rootScope.module = 'feed';
        $rootScope.modulePath = $location.path();

        Feeds.get(function (feeds) {
            $scope.feeds = feeds;
        });

        if ($stateParams.id) {
            Feeds.getOne($stateParams.id, function (feed) {
                $scope.feed = feed;
            })
        }

        $scope.lookup = function () {
            FeedUrlLookupREST.lookup({q: $scope.lookupUrl}, {}, function (data) {
                if (data && data.responseStatus == 200) {
                    $scope.feed = data.responseData;
                }
            });
        };


        $scope.add = function () {
            Feeds.add($scope.feed, function (feeds) {
                $scope.feeds = feeds;
                $location.path('/feed/manage');
            });
        };

        $scope.edit = function () {
            Feeds.edit($scope.feed, function (feeds) {
                $scope.feeds = feeds;
                $location.path('/feed/manage');
            });
        };

        $scope.delete = function (id) {
            if (!confirm('Confirm delete')) {
                return;
            }

            Feeds.remove({id: id}, function (feeds) {
                $scope.feeds = feeds;
                $location.path('/feed/manage');
            });
        };
    });