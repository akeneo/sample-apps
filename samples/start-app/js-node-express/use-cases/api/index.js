const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');
const LogicErrorException = require("../../exceptions/logicError.exception");

const doFirstApiCall = require('./first-api-call');

const firstApiCall = doFirstApiCall({
    httpsClient,
    tokenDb,
    LogicErrorException
});

module.exports = {firstApiCall};
