angular.module('caco.mail.ActionWrapper', ['caco.Credentials'])
    .factory('EMailActionWrapper', function (Credentials) {
        this.wrap = function (resource, actions) {
            var wrappedResource = resource;
            for (var i = actions.length - 1; i >= 0; i--) {
                this.action(wrappedResource, actions[i]);
            }

            return wrappedResource;
        };

        this.action = function (resource, action) {
            resource['_' + action] = resource[action];

            resource[action] = function(data, success, error) {
                return resource['_' + action] (
                    angular.extend({}, data || {}, {
                        key : Credentials.key.email
                    }),
                    success,
                    error
                );
            };
        };

        return this;
    });