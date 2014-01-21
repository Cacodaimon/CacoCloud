angular.module('caco.bookmark.crtl', ['caco.bookmark.REST'])
    .controller('BookMarkCrtl', function ($rootScope, $scope, $stateParams, $location, BookMarkREST, Alerts, Paginator) {
        $rootScope.module = 'bookmark';

        if ($stateParams.id) {
            BookMarkREST.one({id: $stateParams.id}, function (data) {
                $scope.bookmark = data.response;
            });
        }
        if ($location.path() === '/bookmark') {
            BookMarkREST.all({}, function (data) {
                $scope.bookmarks = data.response;
                Paginator.reset();
            });
        }

        $scope.add = function () {
            BookMarkREST.add({}, $scope.newBookmark, function () {
                $location.path('/bookmark');
            }, function () {
                Alerts.addDanger('Could not add the Bookmark!');
            });
        };

        $scope.delete = function (id) {
            if (!confirm('Confirm delete')) {
                return;
            }

            BookMarkREST.remove({id: id}, {}, function(data) {
                if (data.status != 200) {
                    return;
                }

                for (var i = $scope.bookmarks.length - 1; i >= 0; i--) {
                    if ($scope.bookmarks[i].id != id) {
                        continue;
                    }

                    $scope.bookmarks.splice(i, 1);
                }
            }, function () {
                Alerts.addDanger('Could not delete the Bookmark!');
            });
        };

        $scope.edit = function () {
            BookMarkREST.edit({id: $scope.bookmark.id}, $scope.bookmark, function (data) {
                $location.path('/bookmark');
            }, function () {
                Alerts.addDanger('Could not edit the bookmark!');
            });
        };
    });