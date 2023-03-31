const { oauth_scopes } = require('./activation');

let doNotifyAuthorizationUpdate = function({
    httpsClient,
    tokenDb,
    LogicErrorException
 }) {
    return async function notifyAuthorizationUpdate({req, res, next}) {
        if (!await tokenDb.hasToken()) {
            console.log('ici');
            res.render('no_access_token');
            throw new LogicErrorException('Missing access token in database');
        } else {
            const token = await tokenDb.getToken();

            httpsClient.setToken(token);

            const apiUrl = "/connect/apps/v1/scopes/update?scopes=";

            const options = {
                path: apiUrl + encodeURIComponent(oauth_scopes.join(' ')),
                method: 'POST'
            };

            const httpreq = httpsClient.request(options, function (response) {
                let data = '';
                response.on('data', function (chunk) {
                    data += chunk;
                });

                response.on('end', () => {
                    res.render('access_token');
                });
            });

            httpreq.on('error', (e) => {
                console.error(e);
            });
            httpreq.end();
        }
    };
}

module.exports = doNotifyAuthorizationUpdate;
