let doFirstApiCall = function({
    httpsClient,
    tokenDb,
    LogicErrorException
 }) {
    return async function firstApiCall ({req, res, next}) {

        if (!await tokenDb.hasToken()) {
            res.render('no_access_token');
            throw new LogicErrorException('Missing access token in database');
        } else {
            const token = await tokenDb.getToken();

            httpsClient.setToken(token);

            const options = {
                path: "/api/rest/v1/channels",
                method: 'GET'
            };

            const httpreq = httpsClient.request(options, function (response) {
                let data = '';
                response.on('data', (chunk) => {
                    data = data + chunk.toString();
                });

                response.on('end', () => {
                    const body = JSON.parse(data);

                    if (body.hasOwnProperty('code')) {
                        res.status(body.code).json({ message: body.message });
                    } else {
                        res.status(200).json(body);
                    }
                });
            });

            httpreq.on('error', (e) => {
                console.error(e);
            });
            httpreq.end();
        }
    };
}

module.exports = doFirstApiCall;
