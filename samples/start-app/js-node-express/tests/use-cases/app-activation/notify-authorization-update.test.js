require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` })

const { oauth_scopes } = require('../../../use-cases/app-activation/activation');
const doNotifyAuthorizationUpdate = require('../../../use-cases/app-activation/notify-authorization-update');
const {appCallback} = require("../../../use-cases/app-activation");
const LogicError = require("../../../exceptions/logicError.exception");

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
            status: jest.fn().mockReturnThis(),
            json: jest.fn()
        };
        mockNext = jest.fn();
    });

    it('should render "no_access_token" and throw LogicError if token is missing from the database', async () => {
        mockTokenDb.hasToken.mockResolvedValue(false);

        try {
            await doNotifyAuthorizationUpdate({
                httpsClient: mockHttpsClient,
                tokenDb: mockTokenDb,
                LogicError: LogicError,
            });
        } catch (e) {
            expect(mockRes.render).toHaveBeenCalledWith('no_access_token');
            expect(e).toBeInstanceOf(LogicError);
            expect(e.message).toBe('Missing access token in database');
        }
    });

    it('should send a POST request to the API with the correct options and render "access_token" on success', async () => {
        const mockToken = 'mockToken';
        const apiUrl = '/connect/apps/v1/scopes/update?scopes=';
        const options = {
            path: apiUrl + encodeURIComponent(oauth_scopes.join(' ')),
            method: 'POST'
        };
        mockTokenDb.hasToken.mockResolvedValue(true);
        mockTokenDb.getToken.mockResolvedValue(mockToken);
        mockHttpsClient.request.mockResolvedValueOnce({ status: 200, data: { message: 'Success' } });

        await doNotifyAuthorizationUpdate({
            httpsClient: mockHttpsClient,
            tokenDb: mockTokenDb,
            LogicError: LogicError,
        })({
            req: mockReq,
            res: mockRes,
            next: mockNext,
        });

        expect(mockHttpsClient.setToken).toHaveBeenCalledWith(mockToken);
        expect(mockHttpsClient.request).toHaveBeenCalledWith(options);
        expect(mockRes.status).toHaveBeenCalledWith(200);
    });
});
