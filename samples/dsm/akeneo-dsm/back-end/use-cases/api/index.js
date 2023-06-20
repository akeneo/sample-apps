const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');
const { doPimProductsRetrieve } = require('./pim-instance');

const pimProductsRetrieve = doPimProductsRetrieve({
    httpsClient,
    tokenDb
});

module.exports = { pimProductsRetrieve };
