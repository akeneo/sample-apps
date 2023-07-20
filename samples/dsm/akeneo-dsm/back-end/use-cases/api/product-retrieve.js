let doPimProductsRetrieve = function({
    httpsClient,
    tokenDb
}) {
    return async function pimProductsRetrieve(req, res, next) {
        if (!await tokenDb.hasToken()) {
            const tokenMissingMessage = 'Missing access token in the database';
            res.json({access_token: tokenMissingMessage});
        } else {
            const token = await tokenDb.getToken();

            httpsClient.setToken(token);
    
            const options = {
                path: '/api/rest/v1/products?limit=100',
                method: 'GET'
            };
    
            const productsFromPim = await httpsClient.request(options);

            const formattedProducts = formatProducts(productsFromPim._embedded.items);
    
            res.json({products: formattedProducts});
        }
    }

    function formatProducts(products) {
        return products.map((product) => {
            const formattedProduct = {}

            const familyLabel = getFamilyLabel(product.family);
            const categoryLabel = getCategoryLabel(product.category);

            formattedProduct.uuid = product.uuid;
            formattedProduct.identifier = product.identifier;
            formattedProduct.enabled = product.enabled;
            familyLabel.en_US ? formattedProduct.family = familyLabel.en_US : formattedProduct.family = product.family;
            categoryLabel.en_US ? formattedProduct.categories = categoryLabel.en_US : formattedProduct.categories = product.categories;

            return formattedProduct;
        });
    }

    async function getFamilyLabel(familyCode) {
        const options = {
            path: '/api/rest/v1/families/' + familyCode,
            method: 'GET'
        };

        const response = await httpsClient.request(options);

        return response.labels;
    }

    async function getCategoryLabel(categoryCode) {
        const options = {
            path: '/api/rest/v1/categories?search={"code":[{"operator":"IN","value":["' + categoryCode + '"]}]}',
            method: 'GET'
        };

        const response = await httpsClient.request(options);

        return response._embedded.items.labels;
    }
}

module.exports = { doPimProductsRetrieve }
