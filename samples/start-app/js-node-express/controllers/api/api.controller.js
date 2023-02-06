const { firstApiCall } = require('../../use-cases/api');

module.exports = {
    firstApiCall: (req, res, next) => firstApiCall({req, res, next})
};
