require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` })

const {httpsClient} = require("../../client");
const https = require("https");
const http = require("http");
jest.mock('https');

let testData = '{ "test": "test"}';

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

test("It constructs an HttpsClient object", async () => {

    const pimUrl = new URL(process.env.AKENEO_PIM_URL);

    expect(httpsClient.options.host).toBe(pimUrl.hostname);
    expect(httpsClient.options.port).toBe(pimUrl.port);
    expect(httpsClient.options.headers["User-Agent"]).toBe(
        'AkeneoSampleApp/akeneo-dsm'
        + ' Version/' + process.env.APPLICATION_VERSION
        + ' Docker/' + process.env.DOCKER_VERSION
    );
});

test("It sets an acceptable userAgent", async () => {

    let userAgent = httpsClient.buildUserAgent();
    expect(userAgent).toBe('AkeneoSampleApp/akeneo-dsm');

    userAgent = httpsClient.buildUserAgent(process.env.APPLICATION_VERSION);
    expect(userAgent).toBe(
        'AkeneoSampleApp/akeneo-dsm/'
        + process.env.APPLICATION_VERSION
    );

    userAgent = httpsClient.buildUserAgent(process.env.APPLICATION_VERSION, process.env.DOCKER_VERSION);
    expect(userAgent).toBe(
        'AkeneoSampleApp/akeneo-dsm'
        + ' Version/' + process.env.APPLICATION_VERSION
        + ' Docker/' + process.env.DOCKER_VERSION
    );
});

test("It sets a token", async () => {

    let token = { accessToken: "an_access_token" };
    httpsClient.setToken(token);

    expect(httpsClient.options.headers["Authorization"]).toBe("Bearer an_access_token");
});

test("It sends a request", async () => {

    let spyRequest = jest.spyOn(https, 'request').mockImplementation(mockedResponse);

    await httpsClient.request({}, null);

    expect(https.request).toHaveBeenCalled();

    spyRequest.mockRestore();
});
