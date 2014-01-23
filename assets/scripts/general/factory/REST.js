angular.module('caco.general.REST', ['ngResource'])
    .factory('ConfigREST', function ($resource) {
        return $resource('api/1/config/:key', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'},
            edit:   {method: 'PUT'},
            add:    {method: 'POST'}
        });
    });