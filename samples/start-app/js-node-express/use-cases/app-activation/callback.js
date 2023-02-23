let doAppCallback = function({
    strings,
    crypto,
    querystring,
    config,
    httpsClient,
    tokenDb
}) {
    return async function appCallback ({req, res, next}, randomString) {

        if (!req.query.hasOwnProperty('state') || req.query.state !== randomString) {
            console.error("Invalid state");
            res.render('error');
        }

        const codeIdentifier = strings.bin2hex(crypto.randomBytes(30));
        const codeChallenge = crypto.createHash('sha256').update(codeIdentifier + process.env.CLIENT_SECRET).digest('hex');

        const data = querystring.stringify({
            client_id: process.env.CLIENT_ID,
            code: req.query.code,
            grant_type: "authorization_code",
            code_identifier: codeIdentifier,
            code_challenge: codeChallenge
        });

        const options = {
            path: config.get('akeneo.token_request_url'),
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        };

        const httpreq = httpsClient.request(options, function (response) {

            response.setEncoding('utf8');
            response.body = "";

            response.on('data', function (chunk) {
                response.body += chunk;
            });

            response.on('end', function() {
                const access_token = JSON.parse(response.body).access_token;
                tokenDb.upsert({access_token});

                if (!tokenDb.hasToken()) {
                    res.render('no_access_token');
                } else {
                    res.render('access_token');
                }
            });

            response.on('error', function (e) {
               console.log(e);
            });

        });

        httpreq.write(data);
        httpreq.end();
    };
}

module.exports = doAppCallback;
