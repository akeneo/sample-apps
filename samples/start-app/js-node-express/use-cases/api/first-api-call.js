let doFirstApiCall = function({
    config,
    https,
    tokenDb
 }) {
    return async function firstApiCall ({req, res, next}) {

        const pimUrl = new URL(process.env.AKENEO_PIM_URL);

        if (!await tokenDb.hasToken()) {
            res.render('no_access_token');
        } else {
            const token = await tokenDb.getToken();

            const options = {
                host: pimUrl.hostname,
                port: pimUrl.port,
                path: config.get('akeneo.first-call-api-url'),
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token.accessToken}`,
                    'X-APP-SOURCE': 'startApp-nodejs'
                }
            };

            const httpreq = https.request(options, function (response) {
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
