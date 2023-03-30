const { httpsClient } = require('../../client/index');
const { userDb } = require('../../data-access/index');
const LogicError = require('../../exceptions/logicError.exception');
const jwt = require('jsonwebtoken');

const doOpenIdConnect = require('./openId-connect');

const openIdConnect = doOpenIdConnect({
    httpsClient,
    jwt,
    userDb,
    LogicError
});

module.exports = {openIdConnect};
