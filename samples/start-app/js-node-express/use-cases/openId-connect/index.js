const { httpsClient } = require('../../client/index');
const jwt = require('jsonwebtoken');
const { userDb } = require('../../data-access/index')

const doOpenIdConnect = require('./openId-connect');

const openIdConnect = doOpenIdConnect({
    httpsClient,
    jwt,
    userDb
});

module.exports = {openIdConnect};
