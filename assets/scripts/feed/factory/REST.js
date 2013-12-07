angular.module('caco.feed.REST', ['ngResource'])
    .factory('FeedREST', function ($resource) {
        return $resource('api/feed/:id', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'},
            edit:   {method: 'PUT'},
            add:    {method: 'POST'}
        });
    })
    .factory('FeedUpdateREST', function ($resource) {
        return $resource('api/feed/update/:id', {}, {
            perform:    {method: 'GET'}
        });
    })
    .factory('ItemREST', function ($resource) {
        return $resource('api/feed/:id/item/:id_item', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'}
        });
    })
    .factory('FeedCalculateUpdateIntervalsREST', function ($resource) {
        return $resource('api/feed/calculate-update-interval', {}, {
            perform:    {method: 'GET'}
        });
    })
    .factory('FeedUrlLookupREST', function ($resource) {
        return $resource('http://ajax.googleapis.com/ajax/services/feed/lookup', {},{
            lookup: { method: 'JSONP', params: {v: '1.0', callback: 'JSON_CALLBACK'} }
        });
    });