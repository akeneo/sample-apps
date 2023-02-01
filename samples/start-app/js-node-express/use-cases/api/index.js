const config = require('config');
const https = require('https');
const { tokenDb } = require('../../data-access/index');
const path = require('path');

const doFirstApiCall = require('./first-api-call');

const firstApiCall = doFirstApiCall({
    config,
    https,
    tokenDb,
    path
});

module.exports = {firstApiCall};
