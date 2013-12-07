angular.module('caco.password.REST', ['ngResource', 'caco.password.ActionWrapper'])
    .factory('PasswordREST', function ($resource, PasswordActionWrapper) {
        var resource = $resource('api/password/:key/:id', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'},
            edit:   {method: 'PUT'},
            add:    {method: 'POST'}
        });

        return PasswordActionWrapper.wrap(resource, ['one', 'all', 'remove', 'edit', 'add']);
    });