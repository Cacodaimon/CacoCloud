var frisby = require('frisby');
var config = require('./config.js');
var url = config.apiUrl + 'bookmark';

frisby.create('API: List bookmarks')
    .get(url)
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number,
        response: [
            {
                url: String,
                name: String,
                date: Number,
                id: Number
            }
        ]
    })
    .toss();

frisby.create('API: Add a new bookmark')
    .post(url, {
        url: 'http://www.example.com',
        name: 'Example Com'
    }, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(201)
    .expectJSONTypes({
        status: Number,
        response: Number
    })
    .afterJSON(function (api) {
        var id = api.response;

        frisby.create('API: Get the newly added bookmark')
            .get(url + '/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: {
                    url: String,
                    name: String,
                    date: Number,
                    id: Number
                }
            })
            .toss();

        frisby.create('API: Try to add a existing bookmark')
            .post(url, {
                url: 'http://www.example.com',
                name: 'Example Com'
            }, {json: true})
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(500)
            .expectJSONTypes({
                status: Number
            })
            .toss();

        frisby.create('API: Edit the bookmark')
            .put(url + '/' + id, {
                url: 'http://www.example.com',
                name: 'Example Dot Com'
            }, {json: true})
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: Number
            })
            .toss();

        frisby.create('API: Delete the bookmark')
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

frisby.create('API: Get a non existing bookmark')
    .delete(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Delete a non existing bookmark')
    .delete(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Edit a non existing bookmark')
    .put(url + '/' + 1234567890, {
        url: 'http://www.example.com',
        name: 'Example Dot Com'
    }, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Get a bookmark with a non numeric id')
    .get(url + '/NON_NUMERIC_ID')
    .expectStatus(404)
    .toss();

frisby.create('API: Edit a bookmark with a non numeric id')
    .put(url + '/NON_NUMERIC_ID', {})
    .expectStatus(404)
    .toss();

frisby.create('API: Delete a bookmark with a non numeric id')
    .delete(url + '/NON_NUMERIC_ID')
    .expectStatus(404)
    .toss();