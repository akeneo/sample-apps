require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` })

const {appActivate} = require("../../../use-cases/app-activation");
const LogicErrorException = require("../../../exceptions/logicError.exception");

test("It redirects to activate", async () => {

    const req = {
        query: {
            pim_url: "http://a_random_pim_url.com",
        },
        session: jest.fn(),
    };
    const res = {
        redirect: jest.fn(),
    };
    const next = jest.fn();

    const randomString = (Math.random() + 1).toString(36).substring(2);

    await appActivate({req, res, next}, randomString);

    expect(res.redirect).toHaveBeenCalledWith(
        expect.stringMatching(/http:\/\/.*\/connect\/apps\/v1\/authorize\?.*/)
    );
});

test("It throws an error when there is no PIM URL in the request.", async () => {
    const req = {
        query: {},
        session: jest.fn(),
    };
    const res = {
        render: jest.fn(),
        redirect: jest.fn(),
    };
    const next = jest.fn();

    const randomString = (Math.random() + 1).toString(36).substring(2);
    try {
        await appActivate({req, res, next}, randomString);
    } catch (e) {
        expect(e).toBeInstanceOf(LogicErrorException);
        expect(e.message).toBe("Can't retrieve PIM url, please restart the authorization process.");
    }
});
