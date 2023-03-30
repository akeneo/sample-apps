const url = require('url');
const strings = require('locutus/php/strings');
const crypto = require('crypto');
const querystring = require('querystring');
const Codec = require('../../utils/codec.utils');
const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');
const LogicErrorException = require("../../exceptions/logicError.exception");

const doAppActivation = require('./activation');
const doAppCallback = require("./callback");

const appActivate = doAppActivation({
    url,
    LogicErrorException
});

const appCallback = doAppCallback({
    strings,
    crypto,
    querystring,
    httpsClient,
    Codec,
    tokenDb,
    LogicErrorException
});

module.exports = {appActivate, appCallback};
