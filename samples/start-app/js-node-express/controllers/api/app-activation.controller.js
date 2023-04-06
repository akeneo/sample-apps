const { appActivate, appCallback } = require('../../use-cases/app-activation');
const { openIdConnect } = require('../../use-cases/openId-connect');

const randomString = (Math.random() + 1).toString(36).substring(2);

module.exports = {
    appActivate: (req, res, next) => appActivate({req, res, next}, randomString),
    appCallback: (req, res, next) => appCallback({req, res, next}, randomString, openIdConnect)
};
