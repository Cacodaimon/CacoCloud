angular.module('caco.mail.REST', ['ngResource', 'caco.mail.ActionWrapper'])
    .factory('MailAccountREST', function ($resource, EMailActionWrapper) {
        var resource =  $resource('api/mail/:key/account/:id', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'},
            remove: {method: 'DELETE'},
            edit:   {method: 'PUT'},
            add:    {method: 'POST'}
        });

        return EMailActionWrapper.wrap(resource, ['one', 'all', 'remove', 'edit', 'add']);
    })
    .factory('MailBoxesREST', function ($resource, EMailActionWrapper) {
        var resource =  $resource('api/mail/:key/account/:id/mailbox', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'}
        });

        return EMailActionWrapper.wrap(resource, ['one', 'all']);
    })
    .factory('MailHeadersREST', function ($resource, EMailActionWrapper) {
        var resource =  $resource('api/mail/:key/account/:id/mailbox/:mailBoxBase64', {}, {
            one:    {method: 'GET'},
            all:    {method: 'GET'}
        });

        return EMailActionWrapper.wrap(resource, ['one', 'all']);
    })
    .factory('MailREST', function ($resource, EMailActionWrapper) {
        var resource =  $resource('api/mail/:key/account/:id/mailbox/:mailBoxBase64/mail/:uniqueId', {}, {
            one:    {method: 'GET'},
            remove: {method: 'DELETE'}
        });

        return EMailActionWrapper.wrap(resource, ['one', 'remove']);
    })
    .factory('SendMailREST', function ($resource, EMailActionWrapper) {
        var resource =  $resource('api/mail/:key/account/:id/send', {}, {
            send:    {method: 'POST'}
        });

        return EMailActionWrapper.wrap(resource, ['send']);
    });