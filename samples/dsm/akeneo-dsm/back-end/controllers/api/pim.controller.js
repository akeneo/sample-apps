const { pim_instance } = require('../../use-cases/api/pim-instance');

module.exports = {
    instance: (req, res, next) => pim_instance(req, res, next)
}
