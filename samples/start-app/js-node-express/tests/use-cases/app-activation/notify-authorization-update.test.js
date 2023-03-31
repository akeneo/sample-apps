require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` })

const { oauth_scopes } = require('../../../use-cases/app-activation/activation');
const doNotifyAuthorizationUpdate = require('../../../use-cases/app-activation/notify-authorization-update');
const {appCallback} = require("../../../use-cases/app-activation");
const LogicErrorException = require("../../../exceptions/logicError.exception");

describe('doNotifyAuthorizationUpdate', () => {
    let mockHttpsClient;
    let mockTokenDb;
    let mockReq;
    let mockRes;
    let mockNext;

    beforeEach(() => {
        mockHttpsClient = {
            setToken: jest.fn(),
            request: jest.fn(),
        };
        mockTokenDb = {
            hasToken: jest.fn(),
            getToken: jest.fn(),
        };
        mockReq = {};
        mockRes = {
            render: jest.fn(),
        };
        mockNext = jest.fn();
    });

    it('should render "no_access_token" and throw LogicErrorException if token is missing from the database', async () => {
        mockTokenDb.hasToken.mockResolvedValue(false);

        try {
            await doNotifyAuthorizationUpdate({
                httpsClient: mockHttpsClient,
                tokenDb: mockTokenDb,
                LogicErrorException: LogicErrorException,
            });
        } catch (e) {
            expect(mockRes.render).toHaveBeenCalledWith('no_access_token');
            expect(e).toBeInstanceOf(LogicErrorException);
            expect(e.message).toBe('Missing access token in database');
        }
    });

    it('should send a POST request to the API with the correct options and render "access_token" on success', async () => {
        const mockToken = 'mockToken';
        mockTokenDb.hasToken.mockResolvedValue(true);
        mockTokenDb.getToken.mockResolvedValue(mockToken);
        mockHttpsClient.request.mockImplementation((options, callback) => {
            callback({ on: jest.fn() }); // simulate response object
            return { on: jest.fn(), end: jest.fn() }; // return request object
        });

        await doNotifyAuthorizationUpdate({
            httpsClient: mockHttpsClient,
            tokenDb: mockTokenDb,
            LogicErrorException: LogicErrorException,
        })({
            req: mockReq,
            res: mockRes,
            next: mockNext,
        });

        expect(mockHttpsClient.setToken).toHaveBeenCalledWith(mockToken);
        expect(mockHttpsClient.request).toHaveBeenCalledWith({
            path: '/connect/apps/v1/scopes/update?scopes=' + encodeURIComponent(oauth_scopes.join(' ')),
            method: 'POST',
        }, expect.any(Function));
    });
});
