const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');
const { doPimProductsRetrieve } = require('./product-retrieve');

const pimProductsRetrieve = doPimProductsRetrieve({
    httpsClient,
    tokenDb
});

module.exports = { pimProductsRetrieve };
