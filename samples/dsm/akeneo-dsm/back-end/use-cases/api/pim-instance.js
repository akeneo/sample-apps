const pimInstance = function(req, res, next) {
    const myPimInstance = process.env.AKENEO_PIM_URL;
    res.json({'pim-instance': myPimInstance});
}

let doPimProductsRetrieve = function({
    httpsClient,
    tokenDb
}) {
    return async function pimProductsRetrieve(req, res, next) {
        if (!await tokenDb.hasToken()) {
            res.json({'no access token': 'Missing access token in database'});
        } else {
            const token = await tokenDb.getToken();

            httpsClient.setToken(token);
    
            const options = {
                path: '/api/rest/v1/products?limit=100',
                method: 'GET'
            };
    
            const productsFromPim = await httpsClient.request(options);
    
            res.json({products: productsFromPim._embedded.items});
        }
    }
}

module.exports = { pimInstance, doPimProductsRetrieve };
