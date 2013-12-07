angular.module('caco.filter.base64', [])
    .filter('base64Encode', function() {
        return function(data) {
            return window.btoa(data);
        };
    })
    .filter('base64Decode', function() {
        return function(data) {
            return window.atob(data);
        };
    });