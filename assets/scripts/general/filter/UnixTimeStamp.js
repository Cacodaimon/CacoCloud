angular.module('caco.filter', [])
    .filter('unixTimeStamp', function() {
        return function(timeStamp) {
            return timeStamp * 1000;
        };
    });