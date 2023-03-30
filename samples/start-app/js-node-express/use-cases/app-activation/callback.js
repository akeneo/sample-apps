let doAppCallback = function({
    strings,
    crypto,
    querystring,
    httpsClient,
    Codec,
    tokenDb,
    LogicErrorException,
}) {
    return async function appCallback ({req, res, next}, randomString, openIdCallback) {

        if (!req.query.hasOwnProperty('state') || req.query.state !== randomString) {
            res.render('error');
            throw new LogicErrorException('Invalid state');
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
            path: "/connect/apps/v1/oauth2/token",
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

            response.on('end', async function() {
                const access_token = JSON.parse(response.body).access_token;

                if (access_token !== undefined) {
                    tokenDb.upsert({access_token});

                    if (!await tokenDb.hasToken()) {
                        res.render('no_access_token');
                        throw new LogicErrorException('Missing access token in database');
                    } else {
                        if (process.env.OPENID_AUTHENTICATION === "1") {
                            const id_token = JSON.parse(response.body).id_token;
                            const user = await openIdCallback(id_token);
                            const encodedCookie = Codec.encoder(user.sub)
                            res.cookie('sub', encodedCookie.sub);
                            res.cookie('vector', encodedCookie.vector);
                            res.render('access_token', {user: user});
                        } else {
                            res.render('access_token');
                        }
                    }
                } else {
                    res.render('no_access_token');
                    throw new LogicErrorException('Missing access token in response');
                }
            });

            response.on('error', function (e) {
               res.render('error');
            });

        });

        httpreq.write(data);
        httpreq.end();
    };
}

module.exports = doAppCallback;
