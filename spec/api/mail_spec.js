/*
 $this->app->post('/:key/account/:id/send', [$this, 'sendMail']);

*/

var frisby = require('frisby');
var config = require('./config.js');
var sleep = require('sleep').sleep;
var url = config.apiUrl + 'mail/TEST_EMAIL_KEY';


var mailAccountJsonType = {
    name: String,
    imap: {
        host: String,
        port: Number,
        userName: String,
        type: Number,
        ssl: Boolean,
        tls: Boolean,
        noTls: Boolean,
        secure: Boolean,
        validateCert: Boolean
    },
    smtp: {
        host: String,
        port: Number,
        auth: Boolean,
        authType: String,
        userName: String,
        secure: String
    }
};

frisby.create('API: List mail accounts')
    .get(url + '/account')
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number,
        response: [mailAccountJsonType]
    })
    .toss();

frisby.create('API: Add a new mail account')
    .post(url + '/account', {
        name: 'MailTestAccount',
        imap: {
            host: config.popAccount.host,
            port: config.popAccount.port,
            userName: config.popAccount.user,
            password: config.popAccount.password,
            type: 0,
            ssl: false,
            tls: false,
            noTls: false,
            secure: false,
            validateCert: false
        },
        smtp: {
            host: config.mailtrap.host,
            port: config.mailtrap.port,
            userName: config.mailtrap.user,
            password: config.mailtrap.password,
            email: config.mailtrap.email,
            auth: true,
            authType: 'PLAIN',
            realName: 'Caco Cloud'
        }
    }, {json: true})
    .expectStatus(201)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number,
        response: Number
    })
    .afterJSON(function (api) {
        var id = api.response;

        frisby.create('API: Get the newly added bookmark')
            .get(url + '/account/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: mailAccountJsonType
            })
            .toss();

        frisby.create('API: List all mail accounts after creating the new one')
            .get(url + '/account')
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSONTypes({
                status: Number,
                response: [mailAccountJsonType]
            })
            .toss();

        frisby.create('API: List all mail boxes from the new account')
            .get(url + '/account/' + id + '/mailbox')
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSONTypes({
                status: Number,
                response: {
                    name: String,
                    mailBoxes: [{
                        name: String,
                        flags: Number,
                        messages: Number,
                        recent: Number,
                        unseen: Number,
                        uniqueIdNext: Number,
                        uniqueIdValidity: Number,
                        base64Name: String
                    }]
                }
            })
            .toss();

        frisby.create('API: List all mails from the new account INBOX mailbox')
            .get(url + '/account/' + id + '/mailbox/SU5CT1g=')
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSONTypes({
                status: Number,
                response: [{
                    subject: String,
                    from: String,
                    to: String,
                    cc: String,
                    bcc: String,
                    date: String,
                    unixTimeStamp: Number,
                    messageId: String,
                    inReplyToMessageId: String,
                    size: Number,
                    uniqueId: Number,
                    messageNumber: Number,
                    recent: Boolean,
                    flagged: Boolean,
                    answered: Boolean,
                    deleted: Boolean,
                    seen: Boolean,
                    draft: Boolean
                }],
                messagesTotal: Number,
                messagesPerPage: Number,
                page: Number
            })
            .afterJSON(function (api) {
                var uniqueId = api.response[0].uniqueId;
                frisby.create('API: Get  first mail from the new account INBOX mailbox')
                    .get(url + '/account/' + id + '/mailbox/SU5CT1g=/mail/' + uniqueId)
                    .expectStatus(200)
                    .expectHeaderContains('content-type', 'application/json')
                    .expectJSONTypes({
                        status: Number,
                        response: {
                            subject: String,
                            from: String,
                            to: String,
                            cc: String,
                            bcc: String,
                            date: String,
                            unixTimeStamp: Number,
                            messageId: String,
                            inReplyToMessageId: String,
                            size: Number,
                            uniqueId: Number,
                            messageNumber: Number,
                            recent: Boolean,
                            flagged: Boolean,
                            answered: Boolean,
                            deleted: Boolean,
                            seen: Boolean,
                            draft: Boolean,
                            bodyPlainText: String,
                            bodyHtml: String
                        }
                    })
                    .toss();
            })
            .toss();

        var body = 'Body ' + Math.random();
        frisby.create('API: Send a new mail')
            .post(url + '/account/' + id + '/send', {
                subject: 'Test Mail',
                body: body,
                to: 'CacoCloudMailTest@inbox.com'
            }, {json: true})
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSONTypes({
                status: Number,
                response: Boolean
            })
            .afterJSON(function (api) {
                sleep(5); //give mailtrap some seconds...
                frisby.create('API: Checking mail with mailtrap [1/2]')
                    .get(config.mailtrap.getApiUrlMessages())
                    .expectStatus(200)
                    .expectJSONTypes([{
                        message: {
                            id: String,
                            from: String,
                            title: String,
                            created_at: String,
                            recipients: [Object]
                        }
                    }])
                    .afterJSON(function (mailtrap) {
                        frisby.create('API: Checking mail with mailtrap [2/2]')
                            .get(config.mailtrap.getApiUrlMessage(mailtrap[0].message.id))
                            .expectStatus(200)
                            .expectJSONTypes({
                                message: {
                                    id: String,
                                    from: String,
                                    title: String,
                                    created_at: String,
                                    recipients: [Object],
                                    source: String
                                }
                            })
                            .expectBodyContains(body)
                            .toss();

                        frisby.create('API: Edit the newly added mail account')
                            .put(url + '/account/' + id, {
                                name: 'Mail Test Account'
                            }, {json: true})
                            .expectHeaderContains('content-type', 'application/json')
                            .expectStatus(200)
                            .expectJSON({
                                status: 200,
                                response: id
                            })
                            .afterJSON(function (api) {
                                frisby.create('API: Delete the newly added mail account')
                                    .delete(url + '/account/' + id)
                                    .expectHeaderContains('content-type', 'application/json')
                                    .expectStatus(200)
                                    .expectJSON({
                                        status: 200,
                                        response: id
                                    })
                                    .toss();
                            })
                            .toss();
                    })
                    .toss();
            })
            .toss();
    })
    .toss();


frisby.create('API: Get non exisiting mail account')
    .get(url + '/account/11234567890')
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number,
        error: String
    })
    .toss();

frisby.create('API: Edit non exisiting mail account')
    .put(url + '/account/11234567890', {}, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number,
        error: String
    })
    .toss();

frisby.create('API: Delete non exisiting mail account')
    .delete(url + '/account/11234567890')
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number,
        error: String
    })
    .toss();