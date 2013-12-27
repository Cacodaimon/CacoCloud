var frisby = require('frisby');
var config = require('./config.js');
var url = config.apiUrl + 'config';

frisby.create('API: List config')
  .get(url)
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status:   Number,
        response: Array
    })
    .toss();

frisby.create('API: Get config value: "database-version"')
    .get(url + '/database-version')
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status:   Number,
        response: [{
            id: Number,
            key: String,
            value: function(val) { expect(val).toBeType(); }
        }]
    })
    .toss();

frisby.create('API: Add a new config value')
    .post(url, {
        key: 'TEST-KEY-1',
        value: 'TEST-VALUE-1'
    }, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(201)
    .expectJSONTypes({
        status:   Number,
        response: Number
    })
    .afterJSON(function(api) {
        var id = api.response;

        frisby.create('API: Get config value for key: "TEST-KEY-1"')
            .get(url + '/TEST-KEY-1')
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSON({
                status: 200,
                response: [
                    {
                        id: id,
                        key: 'TEST-KEY-1',
                        value: 'TEST-VALUE-1'
                    }
                ]
            })
            .toss();

        frisby.create('API: Edit config value for key: "TEST-KEY-1"')
            .put(url + '/TEST-KEY-1', {
                value: 'TEST-VALUE-ONE'
            }, {json: true})
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSONTypes({
                status:   Number,
                response: Number
            })
            .toss();

        frisby.create('API: Delete config value: "TEST-KEY-1"')
            .delete(url + '/TEST-KEY-1')
            .expectStatus(200)
            .expectHeaderContains('content-type', 'application/json')
            .expectJSONTypes({
                status:   Number,
                response: Number
            })
            .toss();
    })
    .toss();


frisby.create('API: Delete a non existing key: "UNKNOWN-TEST-KEY"')
    .delete(url + '/UNKNOWN-TEST-KEY')
    .expectStatus(404)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Edit a non existing key: "UNKNOWN-TEST-KEY"')
    .delete(url + '/UNKNOWN-TEST-KEY')
    .expectStatus(404)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number
    })
    .toss();