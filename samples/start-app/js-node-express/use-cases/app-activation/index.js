const config = require('config');
const url = require('url');
const strings = require('locutus/php/strings');
const crypto = require('crypto');
const querystring = require('querystring');
const https = require('https');
const { tokenDb } = require('../../data-access/index');

const doAppActivation = require('./activation');
const doAppCallback = require("./callback");

const appActivate = doAppActivation({
    config,
    url,
});

const appCallback = doAppCallback({
    strings,
    crypto,
    querystring,
    config,
    https,
    tokenDb
});

module.exports = {appActivate, appCallback};
