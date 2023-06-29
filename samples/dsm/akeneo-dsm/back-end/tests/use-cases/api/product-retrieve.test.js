describe('doPimProductsRetrieve', () => {
const { doPimProductsRetrieve } = require('../../../use-cases/api/product-retrieve');

 describe('doPimProductsRetrieve', () => {
        it('should return a list of products', async () => {
            const httpsClient = {
                setToken: jest.fn(),
                request: jest.fn().mockResolvedValue({  
                    _embedded: {
                        items: [
                            {
                                uuid: '1',
                                identifier: 'product-1',
                                enabled: true,
                                family: 'family-1',
                                categories: ['category-1', 'category-2']
                            },
                            {
                                uuid: '2',
                                identifier: 'product-2',
                                enabled: true,
                                family: 'family-2',
                                categories: ['category-3', 'category-4']
                            }
                        ]
                    }
                })
            };
            const tokenDb = {
                hasToken: jest.fn().mockResolvedValue(true),
                getToken: jest.fn().mockResolvedValue('token')
            };
            const req = {};
            const res = {
                json: jest.fn()
            };
            const next = jest.fn();

            const pimProductsRetrieve = doPimProductsRetrieve({
                httpsClient,
                tokenDb
            });

            await pimProductsRetrieve(req, res, next);

            expect(res.json).toHaveBeenCalledWith({
                products: [
                    {
                        uuid: '1',
                        identifier: 'product-1',
                        enabled: true,
                        family: 'family-1',
                        categories: ['category-1', 'category-2']
                    },
                    {
                        uuid: '2',
                        identifier: 'product-2',
                        enabled: true,
                        family: 'family-2',
                        categories: ['category-3', 'category-4']
                    }
                ]
            });
        });
    });

    it('should return an error message if no access token is found', async () => {
        const httpsClient = {
            setToken: jest.fn(),
            request: jest.fn()
        };
        const tokenDb = {
            hasToken: jest.fn().mockResolvedValue(false),
            getToken: jest.fn().mockResolvedValue('token')
        };
        const req = {};
        const res = {
            json: jest.fn()
        };
        const next = jest.fn();

        const pimProductsRetrieve = doPimProductsRetrieve({
            httpsClient,
            tokenDb
        });

        await pimProductsRetrieve(req, res, next);

        expect(res.json).toHaveBeenCalledWith({
            'access_token': 'Missing access token in database'
        });
    });
});
