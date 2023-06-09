const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');
const LogicError = require("../../exceptions/logicError.exception");

const doFirstApiCall = require('./first-api-call');

const firstApiCall = doFirstApiCall({
    httpsClient,
    tokenDb,
    LogicError
});

module.exports = {firstApiCall};
