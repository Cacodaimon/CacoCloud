angular.module('caco.bookmark.REST', ['ngResource'])
    .factory('BookMarkREST', function ($resource) {
        return $resource('api/bookmark/:id', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'},
            edit:   {method: 'PUT'},
            add:    {method: 'POST'}
        });
    });