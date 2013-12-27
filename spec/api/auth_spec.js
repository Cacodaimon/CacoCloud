var frisby = require('frisby');
var config = require('./config.js');

frisby.create('Test API Basic Auth')
    .get(config.apiUrlNoAuth + 'config')
    .expectStatus(401)
    .toss();

frisby.create('Test API Basic Auth')
    .get(config.apiUrl + 'config')
    .expectStatus(200)
    .expectHeaderContains('content-type', 'application/json')
    .toss();