angular.module('caco.feed.crtl')
    .controller('ItemCrtl', function ($rootScope, $scope, $stateParams, $location, $anchorScroll, Items, BookMarkREST) {
        Items.one($stateParams, function (item) {
            $scope.item = item;

            $location.hash('feed-item');
            $anchorScroll();
        });

        $scope.addToBookmark = function (item) {
            console.log(item);

            BookMarkREST.add({}, {name: item.title, url: item.url}, function () {
                $location.path('/bookmark');
            });
        };
    });