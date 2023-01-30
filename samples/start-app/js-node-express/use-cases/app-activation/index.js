const config = require('config');
const url = require('url');
const strings = require('locutus/php/strings');
const crypto = require('crypto');
const querystring = require('querystring');
const https = require('https');

const makeAppActivation = require('./activation');
const makeAppCallback = require("./callback");

const appActivate = makeAppActivation({
    config,
    url,
});

const appCallback = makeAppCallback({
    strings,
    crypto,
    querystring,
    config,
    https
});

module.exports = {appActivate, appCallback};
