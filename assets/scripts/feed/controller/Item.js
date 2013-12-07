angular.module('caco.feed.crtl')
    .controller('ItemCrtl', function ($rootScope, $scope, $stateParams, $location, $anchorScroll, Items) {
        Items.one($stateParams, function (item) {
            $scope.item = item;

            $location.hash('feed-item');
            $anchorScroll();
        });
    });