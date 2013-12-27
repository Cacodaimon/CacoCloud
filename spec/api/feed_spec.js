var frisby = require('frisby');
var config = require('./config.js');
var url = config.apiUrl + 'feed';

frisby.create('API: List feeds')
    .get(url)
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .expectJSONTypes({
        status: Number,
        response: Array
    })
    .toss();

frisby.create('API: Update all feeds')
    .get(url + '/update')
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(200)
    .expectJSONTypes({
        status: Number,
        response: [Number]
    })
    .toss();

frisby.create('API: Calculate feed optimal update interval')
    .get(url + '/calculate-update-interval')
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(200)
    .expectJSONTypes({
        status: Number,
        response: Boolean
    })
    .toss();

frisby.create('API: Get all items')
    .get(url + '/item')
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(200)
    .expectJSONTypes({
        status: Number,
        response: [{
            id: Number,
            id_feed: Number,
            title: String,
            date: Number,
            read: Number
        }]
    })
    .toss();

frisby.create('API: Add a new feed')
    .post(url, {
        url: 'http://news.php.net/group.php?group=php.test&format=rss'
    }, {json: true})
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(201)
    .expectJSONTypes({
        status: Number,
        response: Number
    })
    .afterJSON(function (api) {
        var id = api.response;

        frisby.create('API: Update the newly added feed items')
            .get(url + '/update/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: Boolean
            })
            .toss();

        frisby.create('API: Edit the newly added feed')
            .put(url + '/' + id, {
                title: 'My fancy title'
            }, {json: true})
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: Number
            })
            .toss();

        frisby.create('API: Get the newly added feed')
            .get(url + '/' + id)
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSON({
                status: 200,
                response: {
                    url: 'http://news.php.net/group.php?group=php.test&format=rss',
                    title: 'My fancy title'
                }
            })
            .toss();

        frisby.create('API: Get items from the newly added feed')
            .get(url + '/' + id + '/item')
            .expectHeaderContains('content-type', 'application/json')
            .expectStatus(200)
            .expectJSONTypes({
                status: Number,
                response: [{
                    id: Number,
                    id_feed: Number,
                    title: String,
                    date: Number,
                    read: Number
                }]
            })
            .afterJSON(function (api) {
                var itemId = api.response[0].id;

                frisby.create('API: Get items from the newly added feed')
                    .get(url + '/item/' + itemId)
                    .expectHeaderContains('content-type', 'application/json')
                    .expectStatus(200)
                    .expectJSONTypes({
                        status: Number,
                        response: {
                            id: Number,
                            id_feed: Number,
                            title: String,
                            content: String,
                            url: String,
                            date: Number,
                            read: Number
                        }
                    })
                    .afterJSON(function (api) {
                        frisby.create('API: Delete the newly added feed')
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
            })
            .toss();
    })
    .toss();


frisby.create('API: Get a non existing feed')
    .get(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Edit a non existing feed')
    .put(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Edit a non existing feed')
    .delete(url + '/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Get non existing feed item')
    .delete(url + '/item/' + 1234567890)
    .expectHeaderContains('content-type', 'application/json')
    .expectStatus(404)
    .expectJSONTypes({
        status: Number
    })
    .toss();

frisby.create('API: Get a feed with a non numeric id')
    .get(url + '/NON_NUMERIC_ID')
    .expectStatus(404)
    .toss();