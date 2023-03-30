const { httpsClient } = require('../../client/index');
const { userDb } = require('../../data-access/index');
const jwt = require('jsonwebtoken');

const doOpenIdConnect = require('./openId-connect');

const openIdConnect = doOpenIdConnect({
    httpsClient,
    jwt,
    userDb
});

module.exports = {openIdConnect};
