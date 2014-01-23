angular.module('caco.bookmark.REST', ['ngResource'])
    .factory('BookMarkREST', function ($resource) {
        return $resource('api/1/bookmark/:id', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'},
            edit:   {method: 'PUT'},
            add:    {method: 'POST'}
        });
    });