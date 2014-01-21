angular.module('caco.feed.crtl')
    .controller('ItemsCrtl', function ($rootScope, $scope, $stateParams, $location, $anchorScroll, Items, Paginator) {
        Items.all($stateParams.id, function (items) {
            $scope.items = items;
            Paginator.reset();

            $location.hash('feed-items');
            $anchorScroll();
        });
    });