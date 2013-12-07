angular.module('caco.mail', ['ui.router', 'caco.mail.crtl', 'caco.filter.base64', 'caco.Credentials'])
    .config(function($stateProvider) {
        $stateProvider
            .state('mail',                  {                                                templateUrl: 'views/mail/main.html',         controller: 'MailBoxesCrtl' })
            .state('mail.overview',         {url: '/mail',                                   templateUrl: 'views/mail/main/list.html'                                 })
            .state('mail.list',             {url: '/mail/list/:id/:mailBoxBase64',           templateUrl: 'views/mail/main/list.html',    controller: 'MailListCrtl'  })
            .state('mail.read',             {url: '/mail/read/:id/:mailBoxBase64/:uniqueId', templateUrl: 'views/mail/main/read.html',    controller: 'MailReadCrtl'  })
            .state('mail-send',             {url: '/mail/send',                              templateUrl: 'views/mail/send.html',         controller: 'MailSendCrtl'  })
            .state('mail-auth',             {url: '/mail/auth',                              templateUrl: 'views/mail/auth.html',         controller: 'MailAuthCrtl'  })
            .state('mail-manage',           {                                                templateUrl: 'views/mail/manage.html'                                    })
            .state('mail-manage.auth',      {url: '/mail/auth',                              templateUrl: 'views/mail/auth.html',         controller: 'MailManageCrtl'})
            .state('mail-manage.list',      {url: '/mail/manage',                            templateUrl: 'views/mail/manage/list.html',  controller: 'MailManageCrtl'})
            .state('mail-manage.add',       {url: '/mail/manage/add',                        templateUrl: 'views/mail/manage/add.html',   controller: 'MailManageCrtl'})
            .state('mail-manage.edit',      {url: '/mail/manage/edit/:id',                   templateUrl: 'views/mail/manage/edit.html',  controller: 'MailManageCrtl'});
    });