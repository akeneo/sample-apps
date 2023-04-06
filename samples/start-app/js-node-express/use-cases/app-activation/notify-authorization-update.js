const { oauth_scopes } = require('./activation');

let doNotifyAuthorizationUpdate = function({
    httpsClient,
    tokenDb,
    LogicError
 }) {
    return async function notifyAuthorizationUpdate({req, res, next}) {
        if (!await tokenDb.hasToken()) {
            res.render('no_access_token');
            throw new LogicError('Missing access token in database');
        } else {
            const token = await tokenDb.getToken();

            httpsClient.setToken(token);

            const apiUrl = "/connect/apps/v1/scopes/update?scopes=";
            const options = {
                path: apiUrl + encodeURIComponent(oauth_scopes.join(' ')),
                method: 'POST'
            };

            const response = httpsClient.request(options);

            if (response.hasOwnProperty('code')) {
                res.status(response.code).json({ message: response.message });
            } else {
                res.redirect('/');
            }
        }
    };
}

module.exports = doNotifyAuthorizationUpdate;
