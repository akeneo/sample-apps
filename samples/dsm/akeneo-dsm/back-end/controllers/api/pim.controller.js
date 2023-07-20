const { pimInstance } = require('../../use-cases/api/pim-instance');
const { pimProductsRetrieve } = require('../../use-cases/api');

module.exports = {
    instance: (req, res, next) => pimInstance(req, res, next),
    productsRetrieve: (req, res, next) => pimProductsRetrieve(req, res, next)
}
