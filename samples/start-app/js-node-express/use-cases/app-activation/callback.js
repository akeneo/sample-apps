let doAppCallback = function({
    strings,
    crypto,
    querystring,
    config,
    https,
    path,
    tokenDb
}) {
    return async function appCallback ({req, res, next}, randomString) {

        const pimUrl = new URL(process.env.AKENEO_PIM_URL);

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
            host: pimUrl.hostname,
            port: pimUrl.port,
            path: config.get('akeneo.token_request_url'),
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        };

        const httpreq = https.request(options, function (response) {

            response.setEncoding('utf8');
            response.body = "";

            response.on('data', function (chunk) {
                response.body += chunk;
            });

            response.on('end', function() {
                const access_token = JSON.parse(response.body).access_token;
                tokenDb.upsert({access_token});

                if (!tokenDb.hasToken()) {
                    res.sendFile(path.join(__dirname+'/../../views/no_access_token.html'));
                } else {
                    res.sendFile(path.join(__dirname+'/../../views/access_token.html'));
                }
            });
        });

        httpreq.write(data);
        httpreq.end();
    };
}

module.exports = doAppCallback;
