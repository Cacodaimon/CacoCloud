var frisby = require('frisby');
var config = require('./config.js');
var url = config.apiUrl + 'password/TEST_SERVER_KEY';

frisby.create('API: List passwords')
    .get(url)
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number,
        response: Array
    })
    .toss();

frisby.create('API: Add a new password')
    .post(url, {
        name: 'My social network password',
        user: 'MyUserName',
        password: 'MyEncryptedSecretPassword'
    }, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(201)
    .expectJSONTypes({
        status: Number,
        response: Number
    })
    .afterJSON(function (api) {
        var id = api.response;

        frisby.create('API: Get the newly added password')
            .get(url + '/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSON({
                status: Number,
                response: {
                    name: 'My social network password',
                    user: 'MyUserName',
                    password: 'MyEncryptedSecretPassword',
                    id: id
                }
            })
            .toss();

        frisby.create('API: Edit the newly added password')
            .put(url + '/' + id, {
                name: 'My social network password',
                user: 'MyUserName',
                password: 'MyNewEncryptedSecretPassword'
            }, {json: true})
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: Number
            })
            .toss();

        frisby.create('API: Get the newly edited password')
            .get(url + '/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSON({
                status: Number,
                response: {
                    name: 'My social network password',
                    user: 'MyUserName',
                    password: 'MyNewEncryptedSecretPassword',
                    id: id
                }
            })
            .toss();

        frisby.create('API: Delete the newly added password')
            .delete(url + '/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: Number
            })
            .toss();
    })
    .toss();


frisby.create('API: Get a non existing password')
    .delete(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Delete a non existing password')
    .delete(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Edit a non existing password')
    .put(url + '/' + 1234567890, {}, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();


frisby.create('API: Add a password with invalid json')
    .post(url, {
        form: 'data',
        are: 'no',
        json: '...'
    })
    .expectStatus(500)
    .expectJSONTypes({
        status: Number,
        error: String
    })
    .toss();


frisby.create('API: Get a password with a non numeric id')
    .get(url + '/NON_NUMERIC_ID')
    .expectStatus(404)
    .toss();

frisby.create('API: Edit a password with a non numeric id')
    .put(url + '/NON_NUMERIC_ID')
    .expectStatus(404)
    .toss();

frisby.create('API: Delete a password with a non numeric id')
    .delete(url + '/NON_NUMERIC_ID')
    .expectStatus(404)
    .toss();