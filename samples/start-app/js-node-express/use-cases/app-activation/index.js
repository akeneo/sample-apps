const url = require('url');
const strings = require('locutus/php/strings');
const crypto = require('crypto');
const querystring = require('querystring');
const Codec = require('../../utils/codec.utils');
const { httpsClient } = require('../../client/index');
const { tokenDb } = require('../../data-access/index');
const LogicError = require("../../exceptions/logicError.exception");

const { doAppActivate } = require('./activation');
const doAppCallback = require("./callback");
const doNotifyAuthorizationUpdate = require('../app-activation/notify-authorization-update');

const appActivate = doAppActivate({
    url,
    LogicError
});

const appCallback = doAppCallback({
    strings,
    crypto,
    querystring,
    httpsClient,
    Codec,
    tokenDb,
    LogicError
});

const notifyAuthorizationUpdate = doNotifyAuthorizationUpdate({
    httpsClient,
    tokenDb,
    LogicErrorException
});

module.exports = {appActivate, appCallback, notifyAuthorizationUpdate};
