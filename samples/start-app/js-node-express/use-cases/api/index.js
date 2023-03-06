const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');

const doFirstApiCall = require('./first-api-call');

const firstApiCall = doFirstApiCall({
    httpsClient,
    tokenDb
});

module.exports = {firstApiCall};
