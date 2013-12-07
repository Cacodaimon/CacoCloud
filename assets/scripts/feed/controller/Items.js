angular.module('caco.feed.crtl')
    .controller('ItemsCrtl', function ($rootScope, $scope, $stateParams, $location, $anchorScroll, Items) {
        Items.all($stateParams.id, function (items) {
            $scope.items = items;

            $location.hash('feed-items');
            $anchorScroll();
        });
    });