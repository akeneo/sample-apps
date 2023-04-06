let doFirstApiCall = function({
    httpsClient,
    tokenDb,
    LogicError
 }) {
    return async function firstApiCall ({req, res, next}) {

        if (!await tokenDb.hasToken()) {
            res.render('no_access_token');
            throw new LogicError('Missing access token in database');
        } else {
            const token = await tokenDb.getToken();

            httpsClient.setToken(token);

            const options = {
                path: "/api/rest/v1/channels",
                method: 'GET'
            };

            const response = await httpsClient.request(options);

            if (response.hasOwnProperty('code')) {
                res.status(response.code).json({ message: response.message });
            } else {
                res.status(200).json(response);
            }
        }
    };
}

module.exports = doFirstApiCall;
