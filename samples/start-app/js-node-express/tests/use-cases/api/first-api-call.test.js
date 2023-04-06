require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` })

const {firstApiCall} = require("../../../use-cases/api");
const LogicError = require("../../../exceptions/logicError.exception");
const {tokenDb} = require("../../../data-access");
const http = require('http');
const https = require("https");
jest.mock('https')

let testData = JSON.stringify({
    _links: {
        self: {
            href: "http://a_random_pim_url.com/api/rest/v1/channels?page=1&limit=10&with_count=false"
        },
        first: {
            href: "http://a_random_pim_url.com/api/rest/v1/channels?page=1&limit=10&with_count=false"
        }
    },
    current_page: 1,
    _embedded: {
        _links: {
            self: {
                href: "http://a_random_pim_url.com/api/rest/v1/channels/akeneo_onboarder_channel"
            }
        },
        code: "akeneo_onboarder_channel",
        currencies: [
            "USD"
        ],
        locales: [
            "de_DE",
            "en_GB",
            "en_US",
            "fr_FR",
            "ja_JP"
        ],
        category_tree: "master",
        conversion_units: {},
        labels: {
            en_US: "Ecommerce",
            de_DE: "Ecommerce",
            fr_FR: "Ecommerce"
        }
    }
});

const mockedRequest = {
    write: () => null,
    end: () => null,
    on: () => jest.fn(done => done())
}

const mockedResponse = (options, callback) => {
    const response = new http.IncomingMessage();
    callback(response);
    response.emit('data', testData);
    response.emit('end');
    return mockedRequest;
}

https.request.mockImplementation(mockedResponse);

test("It throws an error when no token exists in database", async () => {

    let spyHasToken = jest.spyOn(tokenDb, 'hasToken').mockImplementation(() => false);

    const req = {
        query: {

        },
        session: jest.fn(),
    };
    const res = {
        render: jest.fn()
    };
    const next = jest.fn();

    try {
        await firstApiCall({req, res, next});
    } catch(e) {
        expect(res.render).toHaveBeenCalled();
        expect(res.render).toHaveBeenCalledWith('no_access_token');
        expect(e).toBeInstanceOf(LogicError);
        expect(e.message).toBe("Missing access token in database");
    }

    spyHasToken.mockRestore();
});

test("It retrieves channels", async () => {

    let spyHasToken = jest.spyOn(tokenDb, 'hasToken').mockImplementation(() => true);
    let spyGetToken = jest.spyOn(tokenDb, 'getToken').mockImplementation(() => "an_access_token");

    const req = {
        query: {},
        session: jest.fn(),
    };
    const res = {
        status: jest.fn().mockImplementation(() => res),
        json: jest.fn()
    };
    const next = jest.fn();

    await firstApiCall({req, res, next});

    expect(res.status).toHaveBeenCalled();
    expect(res.status).toHaveBeenCalledWith(200);
    expect(res.json).toHaveBeenCalled();
    expect(res.json).toHaveBeenCalledWith(JSON.parse(testData));

    spyGetToken.mockRestore();
    spyHasToken.mockRestore();
});

test("It retrieves an error", async () => {

    let spyHasToken = jest.spyOn(tokenDb, 'hasToken').mockImplementation(() => true);
    let spyGetToken = jest.spyOn(tokenDb, 'getToken').mockImplementation(() => "an_access_token");

    testData = JSON.stringify({
        code: 401,
        message: "Unauthorized"
    });

    const req = {
        query: {},
        session: jest.fn(),
    };
    const res = {
        status: jest.fn().mockImplementation(() => res),
        json: jest.fn()
    };
    const next = jest.fn();

    await firstApiCall({req, res, next});

    expect(res.status).toHaveBeenCalled();
    expect(res.status).toHaveBeenCalledWith(JSON.parse(testData).code);
    expect(res.json).toHaveBeenCalled();
    expect(res.json).toHaveBeenCalledWith({message : JSON.parse(testData).message});

    spyGetToken.mockRestore();
    spyHasToken.mockRestore();
});
