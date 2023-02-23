const config = require('config');
const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');

const doFirstApiCall = require('./first-api-call');

const firstApiCall = doFirstApiCall({
    config,
    httpsClient,
    tokenDb
});

module.exports = {firstApiCall};
