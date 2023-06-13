require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` });

const {appCallback} = require("../../../use-cases/app-activation");
const LogicError = require("../../../exceptions/logicError.exception");
const {tokenDb} = require("../../../data-access");
const http = require('http');
const https = require("https");
jest.mock('https');

let testData = '{ "access_token": "an_access_token"}';

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

test("It throws an error when there is no state in query", async () => {

    const req = {
        query: {
            code: "authCode"
        },
        session: jest.fn(),
    };
    const res = {
        render: jest.fn()
    };
    const next = jest.fn();

    const randomString = "goodState";

    try {
        await appCallback({req, res, next}, randomString);
    } catch (e) {
        expect(res.render).toHaveBeenCalled();
        expect(e).toBeInstanceOf(LogicError);
        expect(e.message).toBe("Invalid state");
    }
});

test("It throws an error when there is a bad state in query", async () => {

    const req = {
        query: {
            state: "badState",
            code: "authCode"
        },
        session: jest.fn(),
    };
    const res = {
        render: jest.fn()
    };
    const next = jest.fn();

    const randomString = "goodState";

    try {
        await appCallback({req, res, next}, randomString);
    } catch (e) {
        expect(res.render).toHaveBeenCalled();
        expect(e).toBeInstanceOf(LogicError);
        expect(e.message).toBe("Invalid state");
    }
});

test("It retrieves a token", async () => {

    let spyUpsert = jest.spyOn(tokenDb, 'upsert').mockImplementation(() => true);
    let spyHasToken = jest.spyOn(tokenDb, 'hasToken').mockImplementation(() => true);

    const req = {
        query: {
            state: "goodState",
            code: "authCode"
        },
        session: jest.fn(),
    };
    const res = {
        render: jest.fn()
    };
    const next = jest.fn();

    const randomString = "goodState";

    await appCallback({req, res, next}, randomString);

    expect(tokenDb.upsert).toHaveBeenCalled();
    expect(tokenDb.upsert).toHaveBeenCalledWith({access_token: 'an_access_token'});
    expect(res.render).toHaveBeenCalled();
    expect(res.render).toHaveBeenCalledWith('access_token');

    spyUpsert.mockRestore();
    spyHasToken.mockRestore();
});

test("It throws an error when token has not been upsert due to database issue", async () => {

    let spyUpsert = jest.spyOn(tokenDb, 'upsert').mockImplementation(() => true);
    let spyHasToken = jest.spyOn(tokenDb, 'hasToken').mockImplementation(() => false);

    const req = {
        query: {
            state: "goodState",
            code: "authCode"
        },
        session: jest.fn(),
    };
    const res = {
        render: jest.fn()
    };
    const next = jest.fn();

    const randomString = "goodState";

    try {
        await appCallback({req, res, next}, randomString);
    } catch(e) {
        expect(tokenDb.upsert).toHaveBeenCalled();
        expect(res.render).toHaveBeenCalled();
        expect(res.render).toHaveBeenCalledWith('no_access_token');
        expect(e).toBeInstanceOf(LogicError);
        expect(e.message).toBe("Missing access token in database");
    }

    spyUpsert.mockRestore();
    spyHasToken.mockRestore();
});

test("It throws an error when no token has been retrieved in response", async () => {

    let spyUpsert = jest.spyOn(tokenDb, 'upsert').mockImplementation(() => true);
    let spyHasToken = jest.spyOn(tokenDb, 'hasToken').mockImplementation(() => true);

    testData = '{}';

    const req = {
        query: {
            state: "goodState",
            code: "authCode"
        },
        session: jest.fn(),
    };
    const res = {
        render: jest.fn()
    };
    const next = jest.fn();

    const randomString = "goodState";

    try {
        await appCallback({req, res, next}, randomString);
    } catch(e) {
        expect(res.render).toHaveBeenCalled();
        expect(res.render).toHaveBeenCalledWith('no_access_token');
        expect(e).toBeInstanceOf(LogicError);
        expect(e.message).toBe("Missing access token in response");
    }

    spyUpsert.mockRestore();
    spyHasToken.mockRestore();
});
