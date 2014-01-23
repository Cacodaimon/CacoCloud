module.exports = {
    apiUrl: 'http://TEST_USER:TEST_PASSWORD@localhost:8000/api/1/',
    apiUrlNoAuth: 'http://localhost:8000/api/1/',
    mailtrap: {
        host: 'mailtrap.io',
        port: 25,
        user: 'inbox-d41d1bc2fd0e03a4',
        email: 'inbox-d41d1bc2fd0e03a4@mailtrap.io',
        password: '471e2131690eb7ed',
        apiToken: 'O3rSkidx6AqjyFuNLr9nnw',
        getApiUrlMessages: function () {
            return 'http://mailtrap.io/api/v1/inboxes/' + this.user + '/messages?page=1&token=' + this.apiToken;
        },
        getApiUrlMessage: function (messageId) {
            return 'http://mailtrap.io/api/v1/inboxes/' + this.user + '/messages/' + messageId + '?token=' + this.apiToken;
        }
    },
    popAccount: {
        host: 'my.inbox.com',
        port: 110,
        user: 'CacoCloudMailTest',
        password: 'pdGE9umSXqFSpgtX'
    }
}