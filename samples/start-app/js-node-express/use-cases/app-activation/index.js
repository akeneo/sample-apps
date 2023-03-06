const url = require('url');
const strings = require('locutus/php/strings');
const crypto = require('crypto');
const querystring = require('querystring');
const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');

const doAppActivation = require('./activation');
const doAppCallback = require("./callback");

const appActivate = doAppActivation({
    url,
});

const appCallback = doAppCallback({
    strings,
    crypto,
    querystring,
    httpsClient,
    tokenDb
});

module.exports = {appActivate, appCallback};
