require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` });

const fs = require('fs');
const jwt = require('jsonwebtoken');
const {openIdConnect} = require("../../../use-cases/openId-connect");
const LogicError = require("../../../exceptions/logicError.exception");
const http = require("http");
const https = require("https");
const { userDb } = require("../../../data-access");
jest.mock('https');

const tokenContent = '{' +
    ' "iss": "' + process.env.AKENEO_PIM_URL.trim() + '",' +
    ' "sub": "fecd722c-9ff0-40c7-8b56-36f9919da13f",' +
    ' "firstname": "John",' +
    ' "lastname": "Doe",' +
    ' "email_verified": false,' +
    ' "email": "john.doe@email.com"' +
    '}';

const privateKey = fs.readFileSync(__dirname+'/data/mockedPrivateKey.key');
let testDatePrivateKey = privateKey.toString().trim().replace(/[\r\n]/g, '\\r\\n');
let testData = '{ "public_key": "' + testDatePrivateKey + '"}';

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

const mockedError = (options, callback) => {
    const response = new http.IncomingMessage();
    callback(response);
    response.emit('error', "Test Error");
    response.emit('end');
    return mockedRequest;
}

test("It throws an error when there is no token", async () => {
    try {
        await openIdConnect(null);
    } catch (e) {
        expect(e).toBeInstanceOf(LogicError);
        expect(e.message).toBe("Token is not supposed to be null or undefined");
    }
});

test("It throws an error when there is an error in the HTTP Response", async () => {

    https.request.mockImplementation(mockedError);
    let spyUpsert = jest.spyOn(userDb, 'upsert').mockImplementation(() => true);

    try {
        const token = jwt.sign(tokenContent, privateKey, { algorithm: 'RS256' });
        const user = await openIdConnect(token);
    } catch (e) {
        expect(e.message).toBe("Unhandled error. ('Test Error')");
    }

    spyUpsert.mockRestore();
});

test("It returns a user", async () => {

    https.request.mockImplementation(mockedResponse);
    let spyUpsert = jest.spyOn(userDb, 'upsert').mockImplementation(() => true);

    expect.assertions(4);

    try {
        const token = jwt.sign(tokenContent, privateKey, { algorithm: 'RS256' });
        const user = await openIdConnect(token);

        expect(user.firstname).toBe("John");
        expect(user.lastname).toBe("Doe");
        expect(user.email).toBe("john.doe@email.com");
        expect(user.sub).toBe("fecd722c-9ff0-40c7-8b56-36f9919da13f");
    } catch (e) {
        console.error(e.message);
        console.error('it should not reach here');
    }

    spyUpsert.mockRestore();
});
